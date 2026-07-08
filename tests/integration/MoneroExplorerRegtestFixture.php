<?php

/**
 * Constants describing the deterministic Monero regtest chain built by
 * `tests/integration/seed-monero-explorer-chain.php` for the xmrblocks
 * explorer stack in `docker-compose.yml`.
 *
 * The mnemonics and view keys below are regtest-only test fixtures, committed
 * intentionally; the addresses they derive hold no real-world value and never
 * will. The VIEW KEYS are committed because the explorer's `outputs` and
 * `outputsblocks` endpoints take a view key as an input.
 *
 * Values identical on every seed run (heights, addresses, amounts, the fixed
 * payment id) are constants here. Values which differ per run (block hashes,
 * txids — block headers contain timestamps) are written by the seed script to
 * `tests/_data/integration/manifest.json` and read by tests at runtime.
 *
 * @see MODERNIZATION_AND_INTEGRATION_TEST_PLAN.md
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer;

class MoneroExplorerRegtestFixture
{
    public const EXPLORER_URL = 'http://127.0.0.1:8081';
    public const MONERO_DAEMON_RPC_URL = 'http://127.0.0.1:48181';
    public const MONERO_WALLET_RPC_URL = 'http://127.0.0.1:58183';

    public const MINER_WALLET_FILENAME = 'miner_wallet';
    public const MINER_WALLET_PASSWORD = 'integration-test-password';
    public const MINER_WALLET_MNEMONIC =
        'alarms jeers rowboat western cajun fleet unplugs stunning sieve toenail siren omega '
        . 'nostril binocular tadpoles truth comb cohesive pockets eggs lettuce inflamed pool reorder inflamed';
    public const MINER_WALLET_PRIMARY_ADDRESS =
        '472dFiBZLPZWxt3oACDRMQ7D4VmCgyd6bBY9tkqyq6Dw8qCv4onqm3j6rYNmcK5SiGhRahd9EfFu7DY8hXtjJ8RU6R8yNFw';
    public const MINER_WALLET_SECRET_VIEW_KEY =
        '1f2d57ae8857bee62fbdaf48928a5afe9fa1ea8e4051c945023f2ed0fa042f0d';

    public const RECIPIENT_WALLET_FILENAME = 'recipient_wallet';
    public const RECIPIENT_WALLET_PASSWORD = 'integration-test-password';
    public const RECIPIENT_WALLET_MNEMONIC =
        'vehicle metro twofold wept medicate agenda money visited sapling stylishly mittens getting '
        . 'rarest jubilee rotate bunch tell gulp innocent hookup gorilla juggled tobacco unplugs hookup';
    public const RECIPIENT_WALLET_PRIMARY_ADDRESS =
        '4BHdEtKbPcGTodwjfaudBDMtNkTyy9wUwaKoNaNK7YnfF1vZxFUwCrraYysJQ6JAvB6b1CwM7KamdPKYhipNpYWMKWwG2Us';
    public const RECIPIENT_WALLET_SECRET_VIEW_KEY =
        'bf5cd9e99a699c0ff82b9dec3f989248c57585daac6786a7f60d618239b53803';

    /**
     * The 8-byte payment id encoded into RECIPIENT_WALLET_INTEGRATED_ADDRESS.
     */
    public const SEED_PAYMENT_ID = '1122334455667788';

    /**
     * Integrated address: RECIPIENT_WALLET_PRIMARY_ADDRESS + SEED_PAYMENT_ID.
     */
    public const RECIPIENT_WALLET_INTEGRATED_ADDRESS =
        '4LzJFh95zsnTodwjfaudBDMtNkTyy9wUwaKoNaNK7YnfF1vZxFUwCrraYysJQ6JAvB6b1CwM7KamdPKYhipNpYWMUSeyUxpzCc6GQhdzyk';

    /**
     * Coinbase outputs unlock after 60 confirmations, and ring-size-16
     * transactions need at least 16 unlocked outputs on chain.
     */
    public const SEED_BLOCKS_MINED_BEFORE_TRANSFERS = 120;
    public const SEED_BLOCKS_MINED_AFTER_TRANSFERS = 10;

    /**
     * `get_height` after seeding: genesis (1) + 120 + 10.
     */
    public const EXPECTED_CHAIN_HEIGHT_AFTER_SEED = 131;

    /**
     * 1.23 XMR to the recipient's plain address.
     */
    public const PLAIN_TRANSFER_AMOUNT_ATOMIC_UNITS = 1230000000000;

    /**
     * 0.5 XMR to the recipient's INTEGRATED address (carries SEED_PAYMENT_ID).
     */
    public const INTEGRATED_TRANSFER_AMOUNT_ATOMIC_UNITS = 500000000000;

    public const MANIFEST_RELATIVE_PATH = '/../_data/integration/manifest.json';

    public static function getManifestPath(): string
    {
        return __DIR__ . self::MANIFEST_RELATIVE_PATH;
    }

    /**
     * @return array<string,mixed> The manifest written by the seed script.
     * @throws \Exception When the manifest is absent, i.e. the seed script has not run.
     */
    public static function readManifest(): array
    {
        $manifestPath = self::getManifestPath();
        if (!file_exists($manifestPath)) {
            throw new \Exception(
                'Integration fixture manifest not found at ' . $manifestPath
                . '. Run `make integration-up && make integration-seed` first.'
            );
        }
        return json_decode((string) file_get_contents($manifestPath), true, 512, JSON_THROW_ON_ERROR);
    }
}
