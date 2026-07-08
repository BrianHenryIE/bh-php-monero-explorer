# Plan: PHP 8.4 readonly models, local explorer Docker stack, full method + PhpDoc coverage

Implementation plan for modernizing `brianhenryie/bh-php-monero-explorer` — the PHP
client for the [Onion Monero Blockchain Explorer](https://github.com/moneroexamples/onion-monero-blockchain-explorer)
(“xmrblocks”) HTTP API. Written to be executed step-by-step by an AI coding agent.
Each phase has acceptance criteria; do not start a phase until the previous phase's
criteria pass.

This plan deliberately mirrors the conventions already implemented in the sibling
repository `bh-php-monero-rpc` (see its INTEGRATION_TEST_PLAN.md,
PHP84_READONLY_MODELS_PLAN.md, BRICK_MATH_DATETIME_ENUMS_PLAN.md, and
TYPED_RESPONSES_AND_FULL_INTEGRATION_COVERAGE_PLAN.md). Where this plan says
"per the RPC-library convention", consult those files; the essentials are restated
here so this plan is self-contained.

## Current state (verified against the repo)

- `src/ExplorerApi.php`: 14 methods mapping the explorer's GET endpoints;
  `src/ExplorerTools.php`: 3 convenience methods (`getLastBlockHeight`,
  `isBlockContainsPayment`, `verifyPaymentInMempool`).
- 23 model interface + `Model/JsonMapper/*Mapper` pairs (same getter/setter
  pattern the RPC library just retired). Interfaces carry substantial PhpDoc
  (e.g. `NetworkInfo::getBlockSizeLimit()` explains the 100000-block median).
- Tests: `tests/unit/` (mapper fixtures), `tests/contract/` — LIVE calls to
  xmrchain.net configured via a git-ignored `.env.secret`. Non-deterministic,
  needs secrets, tests someone else's server. To be replaced.
- PHP 7.4-era: README badge 7.4, workflows pin/matrix old versions,
  `composer.json` has no `php` requirement at all, `json-mapper/json-mapper: "*"`
  unpinned, `composer.lock` from 2023.
- Known incomplete: `getOutputsBlocks()` has a commented-out "Full JSON parsing
  not yet implemented" warning; several "TODO: cURL" doc notes;
  `ExplorerTools::verifyPaymentInMempool()` returns an untyped `array`.

## Design decisions (do not re-litigate during implementation)

1. **PHP `>=8.4`**, declared in `composer.json` and used in ALL GitHub workflows
   (`codecoverage.yml`, `phpcbf.yml`, `phpstan.yml`, and the new
   `integration-tests.yml`). Regenerate `composer.lock` under 8.4; verify
   `miwebb/jsend` compatibility (small, old package — if it blocks 8.4, inline the
   JSend envelope parsing and drop the dependency; it is ~one method here).
2. **Readonly model classes with constructor property promotion; interfaces
   deleted.** The new class takes the interface's exact FQCN (consumers keep their
   type hints’ names); the `Model/JsonMapper/*Mapper` classes are deleted.
3. **NO PHPDOC IS LOST — hard requirement.** Interface class-level docblocks →
   class docblock; getter docblocks → docblock above the corresponding promoted
   constructor parameter. `@see` links (this library's PhpDoc links into the
   explorer's `main.cpp`/`page.h` line ranges — these are valuable and must
   survive verbatim). A mechanical audit in the final phase compares pre-refactor
   docblock text against the new classes.
4. **Required fields throw; optional fields are explicitly nullable** (the RPC
   library's revised rule): no fabricated defaults. Hydration failures rethrow as
   an `IncompleteExplorerResponseException` naming endpoint, model class, and
   missing property, with the raw body attached via `getResponseBody()` — NOT in
   the message (responses to `outputs`/`outputsblocks` echo the caller's viewkey;
   keep it out of logs). Optionality only with evidence, recorded in the docblock.
5. **One hydrator construction site** (`ExplorerApi::callApi()` currently builds
   its JsonMapper inline): extract, pin `json-mapper/json-mapper` to the same
   version the RPC library pinned, use the constructor middleware, and register
   the same value-object factories.
6. **Adopt the RPC library's value objects and enums**: `MoneroAmount` for atomic
   units — and note this is not theoretical for THIS library: upstream's own
   README example response for `/api/emission` is
   `"coinbase": 14489473877253413000`, which is ALREADY above `PHP_INT_MAX`
   (9223372036854775807); plain `json_decode()` turns it into a lossy float
   today. (`Emission` coinbase/fee, output amounts, `NetworkInfo` fee estimate…),
   `DateTimeImmutable` (UTC) for true epoch timestamps (block/tx timestamps;
   `server_time`), `NetType`-style enums where value sets are closed. Same traps
   apply: durations stay `int`; `unlock_time` stays `int` (height-or-timestamp
   dual meaning); big values need `JSON_BIGINT_AS_STRING` decoding
   (`cumulative_difficulty` is already handled as `string` here — a hint the
   authors met this before). Decide during implementation whether `MoneroAmount`
   is copied in or extracted to a tiny shared package
   (`brianhenryie/bh-php-monero-types`); copying is acceptable pre-1.0, note the
   duplication in both READMEs.
7. **The Docker test server is xmrblocks itself** — the same C++ application this
   library is a client of — backed by a deterministic regtest monerod, with a
   monero-wallet-rpc alongside to create the fixture transactions. Contract tests
   against xmrchain.net and `.env.secret` are retired.

## Phase 1 — Docker stack (the spike is the nettype question)

Target topology (`docker-compose.yml`):

- `monero-daemon` — `ghcr.io/sethforprivacy/simple-monerod` pinned to the same
  `MONERO_VERSION` as the RPC library (`.env`), `--regtest --fixed-difficulty 1
  --offline`-equivalent flags per the RPC library's compose file (single daemon;
  no peer needed here). Its data dir on a NAMED VOLUME shared read-only with
  xmrblocks (`--bc-path` reads the LMDB directly).
- `monero-wallet-rpc-fixture` — pinned wallet-rpc for seeding
  (`--allow-mismatched-daemon-version` — required on regtest).
- `xmrblocks` — built from the upstream repo's own `Dockerfile` at a PINNED commit
  (confirmed: upstream ships a Dockerfile — builds against the latest monero
  release tag, `--build-arg NPROC=<n>` controls build parallelism, needs ~3 GB
  build space, final image ~179 MB — and upstream's README even ships a
  docker-compose example pairing xmrblocks with the SAME
  `ghcr.io/sethforprivacy/simple-monerod` image the RPC library uses).
  The C++ build is long: build once and push to
  `ghcr.io/brianhenryie/onion-monero-blockchain-explorer:<commit>` (document the
  rebuild command in the compose file header), pull thereafter. Flags:
  `--daemon-url monero-daemon:18081`, `--enable-json-api` (the JSON API is
  OPT-IN), `--enable-emission-monitor` (required for `/api/emission`).
  Port `127.0.0.1:8081:8081`. Healthcheck: `curl --fail
  http://localhost:8081/api/networkinfo`.
  NB (confirmed upstream): the emission monitor WRITES
  `lmdb/emission_amount.txt` into the blockchain data dir — so the shared
  volume can NOT be mounted read-only into xmrblocks, and only one explorer
  instance may run per nettype/data-dir. Mount read-write; the explorer never
  writes to the LMDB itself.

**SPIKE (gating):** xmrblocks has `-t/--testnet` and `--stagenet` flags but no
regtest flag. Regtest is mainnet-formatted ("fakechain"), so first try xmrblocks
in default mainnet mode against the regtest daemon/LMDB. If it rejects the chain
(genesis check or nettype probe), fall back to a **private testnet**: monerod
`--testnet --fixed-difficulty 1 --offline`, xmrblocks `-t`, and seed by actually
mining (`start_mining` at difficulty 1 — budget for RandomX init ~20s; the RPC
library's `generateblocks` shortcut is regtest-only). Record the outcome in this
file's implementation notes. Everything downstream is nettype-agnostic except the
fixture addresses/mnemonics.

Makefile: `integration-up` / `integration-seed` / `integration-down` /
`integration`, exactly as in the RPC library.

**Acceptance criteria:** `make integration-up` from nothing reaches healthy;
`curl http://127.0.0.1:8081/api/networkinfo` returns the seeded/empty chain's
info; `docker compose down -v` leaves no state.

## Phase 2 — Deterministic seed + fixtures

Mirror the RPC library's pattern (fresh mnemonics for THIS repo — different
nettype may force different address formats anyway):

1. `tests/integration/MoneroExplorerRegtestFixture.php`: two committed test-only
   mnemonics + addresses + view keys (the explorer's `outputs`/`outputsblocks`
   endpoints need a VIEW KEY as a fixture — this library is the reason the view
   key must be committed too), expected heights/balances as constants.
2. `tests/integration/seed-monero-explorer-chain.php` (uses `bh-php-monero-rpc`
   as a dev dependency for Daemon/Wallet calls — it is the natural consumer):
   restore wallets → mine 120 to the miner → transfer 1.23 XMR to the recipient →
   transfer 0.5 XMR to an INTEGRATED address of the recipient (gives
   `isBlockContainsPayment`/payment-id scenarios real data) → mine 10 →
   wait until xmrblocks reports the final height → write
   `tests/_data/integration/manifest.json` (txids, block hashes, payment id,
   integrated address, tx keys). Idempotent, refuses odd chain states.
3. Run twice from clean state; promote run-invariant values to fixture constants;
   run-specific values stay manifest-only.

**Acceptance criteria:** seed idempotent; two clean-slate runs produce identical
constants; xmrblocks `/api/transaction/<manifest txid>` returns the seeded tx.

## Phase 3 — PHP 8.4 platform + readonly model conversion

1. Platform: `composer.json` `"php": ">=8.4"`, pin json-mapper, `composer update`
   under 8.4, all workflows to 8.4, README badge.
2. Convert all 23 models (dependency order — leaf classes first):
   `RawTransactionVoutTarget`, `RawTransactionVinKey`, `RawTransactionVout`,
   `RawTransactionVin`, `RawTransaction`, `RawBlockMinerTx`, `RawBlock`,
   `BlockTx`, `Block`, `Transaction`, `DetailedTransaction`, `TransactionsBlock`,
   `Transactions`, `MempoolTxs`, `Mempool`, `OutputsOutput`, `Outputs`,
   `OutputsBlocksOutput`, `OutputsBlocks`, `NetworkInfo`, `Emission`, `Search`,
   `Version` — new readonly class in the interface's file, docblocks transferred
   per design decision 3, mapper deleted, `ExplorerApi` references updated.
3. Centralize the hydrator + add `IncompleteExplorerResponseException` wrapping
   (design decisions 4–5). Keep the JSend envelope handling (`status`/`data`)
   where it is, but decode the body ONCE with `JSON_BIGINT_AS_STRING`.
4. Value objects/enums per design decision 6, with the same factory pattern and
   permanent strictness tests (missing-required throws; optional-null; extra
   ignored; >PHP_INT_MAX value lossless).
5. Update `tests/unit/model/jsonmapper/MappersTest.php` to the new class names
   and the shared hydrator. (NB: the RPC repo's `MappersTest` historically
   carried this repo's `BrianHenryIE\MoneroExplorer` namespace — when copying
   anything between the repos, fix namespaces.)

**Acceptance criteria:** `src/Model/JsonMapper/` gone; unit suite green;
`composer lint` green on 8.4; docblock-preservation audit (Phase 6) queued.

## Phase 4 — Missing methods + method completion

1. **Enumerate the pinned xmrblocks commit's handlers** (the library's own
   docblocks link `main.cpp#L656-L837` — regenerate this list from the PINNED
   commit, not the 2023 one). The upstream README's documented JSON API is:
   `transaction`, `rawtransaction`, `block`, `rawblock`, `transactions`,
   `mempool`, `search`, `outputs`, `outputsblocks`, `networkinfo`, `emission`,
   `version` — which the library's 14 methods appear to cover
   (`getDetailedTransaction` wraps `transaction` — verify how). So "missing
   methods" is expected to be small-to-empty for GET endpoints; the remaining
   questions are whether `--enable-pusher` exposes any JSON (vs HTML-only)
   raw-tx submit/check endpoints, and whether anything was added upstream since
   2023. For each added method: typed readonly model + fixture JSON +
   MappersTest entry + integration test (one work unit, per the RPC-library
   convention).
2. Finish `getOutputsBlocks()` parsing (the "not yet implemented" comment must
   die: hydrate the full response shape, verified against live seeded data).
3. Type `ExplorerTools::verifyPaymentInMempool()` (returns bare `array`) and
   review `ExplorerTools` generally — its convenience methods should accept/return
   the value objects (`MoneroAmount`, models) rather than primitives.
4. Update the two `TODO: cURL` docblocks with real, copy-pasteable `curl`
   examples against the LOCAL stack (`curl 'http://127.0.0.1:8081/api/outputs?...'`).

**Acceptance criteria:** the handler-enumeration list is committed in this file's
implementation notes with each endpoint marked present/added/not-applicable
(with reason); no TODO markers remain in `src/`.

## Phase 5 — Integration test suites

Replace `tests/contract/` with the three-suite layout (`unit`, `integration`,
`integration-mutating`), `defaultTestSuite="unit"`, `composer test-integration`,
and a base test case that FAILS (never skips) when the stack is absent.

Read-only (`tests/integration/`), all against manifest/constants:
`getTransaction`, `getRawTransaction`, `getDetailedTransaction`, `getBlock` (by
height AND by hash), `getRawBlock`, `getTransactions` (pagination params),
`getSearch` (by height, by txid, by block hash — and the not-found shape),
`getNetworkInfo` (height, nettype fields vs the chosen nettype),
`getEmission` (coinbase = sum of seeded rewards — deterministic!, fee > 0),
`getOutputs` — the crown jewel: with the recipient's address + committed view key
against the seed txid, assert the discovered output amount equals 1.23 XMR
exactly; with `txprove=true` and the tx key, prove the SENDING direction;
`getOutputsBlocks` (recent-blocks scan finds the integrated-address payment),
`getVersion`, `ExplorerTools::getLastBlockHeight`,
`ExplorerTools::isBlockContainsPayment` (true for the seeded payment block,
false for a coinbase-only block).

Mutating (`tests/integration-mutating/`): create an UNCONFIRMED transfer via the
fixture wallet-rpc → `getMempool` shows it, `verifyPaymentInMempool` finds the
integrated-address payment in the pool, `getOutputsBlocks(mempool: true)` sees it
→ mine to confirm in `finally`. Plus any pusher endpoints added in Phase 4
(submit a `do_not_relay` raw tx through the explorer, assert accepted).

CI: `.github/workflows/integration-tests.yml` per the RPC library (checkout, PHP
8.4, compose up --wait, seed, suites, logs-on-failure, down -v). Plus the
mechanical coverage audit script: every public method of `ExplorerApi` and
`ExplorerTools` must be referenced by an integration test, enforced in CI.

**Acceptance criteria:** `make integration` green twice consecutively;
`tests/contract/` and the `.env.secret` requirement deleted; audit green.

## Phase 6 — Concept PhpDoc for consumers + final audit

The README already states the thesis: "this library's value comes from the typed
classes and the documentation in PhpDoc." This phase makes that true for Monero
CONCEPTS, not just field names. Rules: concept explanations live on the class or
property where a consumer first meets the concept; write for a PHP developer who
has never read Monero documentation; link primary sources
(https://www.getmonero.org/resources/moneropedia/ entries, the explorer's
README/source) with `@see`; 3–8 sentences each, no marketing.

Required coverage (minimum):

- `Outputs`/`OutputsOutput` + `getOutputs()`: stealth addresses — why recipients
  are not visible on-chain; what a VIEW KEY is and what it can/cannot reveal
  (incoming, not outgoing); what `txprove` does (tx key proves SENDING to an
  address; view key proves RECEIVING). **A prominent privacy warning on
  `getOutputs()` and `getOutputsBlocks()`: submitting an address + view key to a
  third-party explorer permanently discloses those incoming transactions to that
  server — recommend a self-hosted instance (this repo now ships one in Docker).**
- `RawTransactionVin`/`RawTransactionVinKey`: key images (double-spend
  prevention) and ring members/decoys — why `vin` references many outputs of
  which only one is really spent.
- `Transaction`/`DetailedTransaction`: confirmations, `unlock_time` (height vs
  timestamp dual meaning — same docblock trap-warning as the RPC library),
  payment IDs and why standalone ones are deprecated in favor of integrated
  addresses; fee vs amount (amounts are hidden by RingCT; the explorer only
  knows amounts the view key can decode).
- `Emission`: what emission is (coinbase + tail emission), why the endpoint
  requires `--enable-emission-monitor`, and that values are atomic units
  (1 XMR = 1e12) — with `MoneroAmount` making that explicit.
- `Mempool`: unconfirmed transactions, why mempool contents differ per node.
- `NetworkInfo`: difficulty (and the 64-bit split fields), block weight/size
  median semantics (the existing good PhpDoc here is the model for the rest).
- `ExplorerApi` class docblock: what an explorer IS relative to a node (an
  indexer over public chain data), the JSend response envelope, and the trust
  model (an explorer can lie; proofs via `txprove`/view key are verifiable).

Finally:

1. **PhpDoc preservation audit** (design decision 3): compare every deleted
   interface's docblock text against the new classes; zero lost sentences.
2. README: new usage example (readonly properties), Docker/testing section,
   PHP 8.4 badge, privacy-warning paragraph mirroring `getOutputs()`.
3. Implementation-notes addendum in this file (spike outcome, deviations,
   endpoint enumeration table).

**Acceptance criteria:** audit reports zero lost docblocks; every class in
`src/Model/` has a non-trivial class-level docblock; `composer test`,
`composer lint`, `make integration` all green.

## Known risks / gotchas for the implementing agent

- The Phase 1 nettype spike gates everything; do it before writing any test.
- xmrblocks reads the LMDB directly: filesystem-share it READ-ONLY, and expect it
  to lag monerod by a few seconds after mining — `pollUntil` on
  `/api/networkinfo` height before asserting.
- `/api/emission` needs `--enable-emission-monitor` AND takes time to scan on
  first run — the healthcheck must not depend on it; the emission test should
  poll.
- The explorer echoes view keys in URLs: never log request URLs in test failure
  output beyond the endpoint name (fixture keys are valueless, but the habit
  matters — consumers copy test patterns).
- `search` returns heterogeneous shapes (block vs tx vs not-found) — the `Search`
  model must express that honestly (nullable groups or subtype objects), not
  fabricate defaults.
- Do not modify the sibling RPC library from this plan; if `MoneroAmount` is
  shared via a new package, that extraction is its own small task with its own
  tests.
