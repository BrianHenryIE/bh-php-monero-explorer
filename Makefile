# Integration test environment.
# See MODERNIZATION_AND_INTEGRATION_TEST_PLAN.md and docker-compose.yml.

# Start monerod, monero-wallet-rpc, and the xmrblocks explorer; wait until healthy.
# The FIRST run compiles xmrblocks from source (~20 minutes, ~3 GB build space).
integration-up:
	docker compose up -d --wait

# Reset the regtest chain to a clean slate and (re)build the deterministic chain,
# writing tests/_data/integration/manifest.json. Safe to run repeatedly: the
# mutating test suite leaves the chain dirty and the seed script only accepts a
# clean chain, so this wipes the blockchain volume first.
#
# It refreshes DATA ONLY — it never recompiles. `docker compose down -v` removes
# the containers and the blockchain volume but NOT the images, and `--no-build`
# makes `up` reuse the already-built xmrblocks/monerod images (erroring, rather
# than silently starting the ~20-minute build, if they are missing). Run
# `make integration-up` once first to build them.
#
# The trailing restart works around xmrblocks' emission monitor, which only scans
# to the chain tip on startup against a POPULATED chain; xmrblocks first came up
# on the empty pre-seed chain, so without this /api/emission stays stuck at block 0.
integration-seed:
	docker compose down -v
	docker compose up -d --wait --no-build
	php tests/integration/seed-monero-explorer-chain.php
	docker compose restart xmrblocks
	docker compose up -d --wait --no-build xmrblocks

# Destroy the containers AND all chain/wallet state.
integration-down:
	docker compose down -v

integration-logs:
	docker compose logs

# Full cycle: build/start the stack, seed (which resets the chain), run the
# integration suites, tear down.
integration:
	$(MAKE) integration-up
	$(MAKE) integration-seed
	MONERO_INTEGRATION_TESTS=1 composer test-integration
	$(MAKE) integration-down
