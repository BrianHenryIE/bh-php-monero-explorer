#!/usr/bin/env php
<?php

/**
 * Builds the deterministic Monero regtest chain the explorer integration tests
 * assert against. Run after `make integration-up`.
 *
 * Sequence:
 *   1. Restore the two deterministic wallets from their committed mnemonics.
 *   2. Mine 120 blocks to the miner wallet (coinbase unlocks after 60).
 *   3. Transfer 1.23 XMR to the recipient's plain address, and 0.5 XMR to the
 *      recipient's INTEGRATED address (fixed payment id — gives the explorer's
 *      payment-checking endpoints real data).
 *   4. Mine 10 more blocks. Final height: 131.
 *   5. Wait for xmrblocks to index to the final height (skipped, with a
 *      warning, if the explorer is not reachable — e.g. in environments that
 *      can only run monerod).
 *   6. Write run-specific values to tests/_data/integration/manifest.json.
 *
 * Talks JSON-RPC to monerod/monero-wallet-rpc directly (Guzzle) so this
 * repository needs no dependency on the sibling RPC library. Idempotent.
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

declare(strict_types=1);

namespace BrianHenryIE\MoneroExplorer;

use Exception;
use GuzzleHttp\Client;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/MoneroExplorerRegtestFixture.php';

if (PHP_SAPI !== 'cli') {
    exit(1);
}

$httpClient = new Client(['timeout' => 30, 'http_errors' => false]);

/**
 * @param array<string,mixed> $params
 * @return array<string,mixed> The `result`.
 */
function jsonRpc(Client $client, string $url, string $method, array $params = []): array
{
    $response = $client->post($url . '/json_rpc', [
        'json' => ['jsonrpc' => '2.0', 'id' => '0', 'method' => $method, 'params' => (object) $params],
    ]);
    /** @var array{error?: array{message: string}, result?: array<string,mixed>} $decoded */
    $decoded = json_decode((string) $response->getBody(), true, 512, JSON_BIGINT_AS_STRING | JSON_THROW_ON_ERROR);
    if (isset($decoded['error'])) {
        throw new Exception("{$method}: {$decoded['error']['message']}");
    }
    return $decoded['result'] ?? [];
}

/**
 * @param callable():bool $condition
 */
function pollUntil(callable $condition, int $timeoutSeconds, string $failureMessage): void
{
    $start = time();
    while (time() - $start < $timeoutSeconds) {
        try {
            if ($condition()) {
                return;
            }
        } catch (Exception $exception) {
            // Not ready yet.
        }
        usleep(250000);
    }
    fwrite(STDERR, "Timeout: {$failureMessage}\n");
    exit(1);
}

$daemonUrl = getenv('MONERO_DAEMON_RPC_URL') ?: MoneroExplorerRegtestFixture::MONERO_DAEMON_RPC_URL;
$walletUrl = getenv('MONERO_WALLET_RPC_URL') ?: MoneroExplorerRegtestFixture::MONERO_WALLET_RPC_URL;
$explorerUrl = getenv('MONERO_EXPLORER_URL') ?: MoneroExplorerRegtestFixture::EXPLORER_URL;

$daemon = fn(string $method, array $params = []): array => jsonRpc($httpClient, $daemonUrl, $method, $params);
$wallet = fn(string $method, array $params = []): array => jsonRpc($httpClient, $walletUrl, $method, $params);

echo "Waiting for monerod and monero-wallet-rpc...\n";
pollUntil(fn() => ($daemon('get_block_count')['count'] ?? 0) >= 1, 60, 'monerod RPC unreachable');
pollUntil(fn() => isset($wallet('get_version')['version']), 60, 'monero-wallet-rpc unreachable');

$initialHeight = (int) $daemon('get_block_count')['count'];
$manifestPath = MoneroExplorerRegtestFixture::getManifestPath();

if ($initialHeight === MoneroExplorerRegtestFixture::EXPECTED_CHAIN_HEIGHT_AFTER_SEED && file_exists($manifestPath)) {
    echo "Chain already seeded (height {$initialHeight}) and manifest present. Nothing to do.\n";
    exit(0);
}
if ($initialHeight !== 1) {
    fwrite(STDERR, "Chain height {$initialHeight}; expected 1 (unseeded) or "
        . MoneroExplorerRegtestFixture::EXPECTED_CHAIN_HEIGHT_AFTER_SEED
        . " (seeded). Run `make integration-down` for a clean slate.\n");
    exit(1);
}

echo "Restoring deterministic wallets...\n";
$minerRestore = $wallet('restore_deterministic_wallet', [
    'filename' => MoneroExplorerRegtestFixture::MINER_WALLET_FILENAME,
    'password' => MoneroExplorerRegtestFixture::MINER_WALLET_PASSWORD,
    'seed' => MoneroExplorerRegtestFixture::MINER_WALLET_MNEMONIC,
    'restore_height' => 0,
]);
if ($minerRestore['address'] !== MoneroExplorerRegtestFixture::MINER_WALLET_PRIMARY_ADDRESS) {
    fwrite(STDERR, "Miner wallet restored to unexpected address {$minerRestore['address']}\n");
    exit(1);
}

echo 'Mining ' . MoneroExplorerRegtestFixture::SEED_BLOCKS_MINED_BEFORE_TRANSFERS . " blocks to the miner wallet...\n";
$daemon('generateblocks', [
    'amount_of_blocks' => MoneroExplorerRegtestFixture::SEED_BLOCKS_MINED_BEFORE_TRANSFERS,
    'wallet_address' => MoneroExplorerRegtestFixture::MINER_WALLET_PRIMARY_ADDRESS,
    'starting_nonce' => 0,
]);

$wallet('refresh');
pollUntil(
    fn() => ($wallet('get_balance')['unlocked_balance'] ?? 0) > 0,
    60,
    'Miner wallet shows no unlocked balance after mining'
);

echo "Transferring 1.23 XMR to the recipient's plain address...\n";
$plainTransfer = $wallet('transfer', [
    'destinations' => [[
        'amount' => MoneroExplorerRegtestFixture::PLAIN_TRANSFER_AMOUNT_ATOMIC_UNITS,
        'address' => MoneroExplorerRegtestFixture::RECIPIENT_WALLET_PRIMARY_ADDRESS,
    ]],
    'get_tx_key' => true,
]);

echo "Transferring 0.5 XMR to the recipient's integrated address (payment id "
    . MoneroExplorerRegtestFixture::SEED_PAYMENT_ID . ")...\n";
$integratedTransfer = $wallet('transfer', [
    'destinations' => [[
        'amount' => MoneroExplorerRegtestFixture::INTEGRATED_TRANSFER_AMOUNT_ATOMIC_UNITS,
        'address' => MoneroExplorerRegtestFixture::RECIPIENT_WALLET_INTEGRATED_ADDRESS,
    ]],
    'get_tx_key' => true,
]);

echo 'Mining ' . MoneroExplorerRegtestFixture::SEED_BLOCKS_MINED_AFTER_TRANSFERS . " blocks to confirm...\n";
$daemon('generateblocks', [
    'amount_of_blocks' => MoneroExplorerRegtestFixture::SEED_BLOCKS_MINED_AFTER_TRANSFERS,
    'wallet_address' => MoneroExplorerRegtestFixture::MINER_WALLET_PRIMARY_ADDRESS,
    'starting_nonce' => 0,
]);

$expectedFinalHeight = MoneroExplorerRegtestFixture::EXPECTED_CHAIN_HEIGHT_AFTER_SEED;
pollUntil(
    fn() => (int) $daemon('get_block_count')['count'] === $expectedFinalHeight,
    60,
    "Chain did not reach final height {$expectedFinalHeight}"
);

$wallet('refresh');
$minerBalance = $wallet('get_balance');

// The tx-containing block: transfers entered the pool at height 121 and were
// mined into the next block.
$transfersBlockHeight = 1 + MoneroExplorerRegtestFixture::SEED_BLOCKS_MINED_BEFORE_TRANSFERS;
$plainTransferDetail = $wallet('get_transfer_by_txid', ['txid' => $plainTransfer['tx_hash']]);

echo "Waiting for xmrblocks to index to height {$expectedFinalHeight}...\n";
$explorerHeight = null;
try {
    $networkInfoResponse = $httpClient->get($explorerUrl . '/api/networkinfo', ['timeout' => 5]);
    if ($networkInfoResponse->getStatusCode() === 200) {
        pollUntil(
            function () use ($httpClient, $explorerUrl, $expectedFinalHeight, &$explorerHeight): bool {
                $body = json_decode(
                    (string) $httpClient->get($explorerUrl . '/api/networkinfo')->getBody(),
                    true
                );
                $explorerHeight = $body['data']['height'] ?? null;
                return $explorerHeight >= $expectedFinalHeight;
            },
            120,
            'xmrblocks did not index the seeded chain'
        );
    } else {
        fwrite(
            stream: STDERR,
            data: "WARNING: xmrblocks returned HTTP {$networkInfoResponse->getStatusCode()}; seeding chain only.\n"
        );
    }
} catch (Exception $exception) {
    fwrite(STDERR, "WARNING: xmrblocks not reachable at {$explorerUrl}; seeding chain only.\n");
}

$manifest = [
    'seeded_at' => date('c'),
    'chain_height' => (int) $daemon('get_block_count')['count'],
    'explorer_indexed_height' => $explorerHeight,
    'genesis_block_hash' => $daemon('get_block_header_by_height', ['height' => 0])['block_header']['hash'],
    'transfers_block_height' => (int) ($plainTransferDetail['transfer']['height'] ?? $transfersBlockHeight),
    'transfers_block_hash' => $daemon(
        'get_block_header_by_height',
        ['height' => (int) ($plainTransferDetail['transfer']['height'] ?? $transfersBlockHeight)]
    )['block_header']['hash'],
    'plain_transfer_txid' => $plainTransfer['tx_hash'],
    'plain_transfer_tx_key' => $plainTransfer['tx_key'],
    'plain_transfer_fee_atomic_units' => $plainTransfer['fee'],
    'integrated_transfer_txid' => $integratedTransfer['tx_hash'],
    'integrated_transfer_tx_key' => $integratedTransfer['tx_key'],
    'integrated_transfer_fee_atomic_units' => $integratedTransfer['fee'],
    'miner_wallet_balance_atomic_units' => $minerBalance['balance'],
];

if (!is_dir(dirname($manifestPath))) {
    mkdir(dirname($manifestPath), 0777, true);
}
file_put_contents($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

echo "Seeded. Manifest written to {$manifestPath}:\n";
echo json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
exit(0);
