<?php

/**
 * Base class for integration tests against the seeded xmrblocks Docker stack.
 *
 * Prerequisites (see Makefile / MODERNIZATION_AND_INTEGRATION_TEST_PLAN.md):
 *
 *     make integration-up
 *     make integration-seed
 *
 * These tests FAIL (never skip) when the stack is unreachable or unseeded,
 * so CI cannot silently pass.
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\TestCase;

abstract class MoneroExplorerIntegrationTestCase extends TestCase
{
    protected static ExplorerTools $explorerApiClient;

    /** Plain HTTP client for talking JSON-RPC to monerod/wallet-rpc directly. */
    protected static Client $rawHttpClient;

    /** @var array<string,mixed> */
    protected static array $manifest;

    protected static string $explorerUrl;
    protected static string $daemonRpcUrl;
    protected static string $walletRpcUrl;

    public static function setUpBeforeClass(): void
    {
        self::$explorerUrl = getenv('MONERO_EXPLORER_URL') ?: MoneroExplorerRegtestFixture::EXPLORER_URL;
        self::$daemonRpcUrl = getenv('MONERO_DAEMON_RPC_URL') ?: MoneroExplorerRegtestFixture::MONERO_DAEMON_RPC_URL;
        self::$walletRpcUrl = getenv('MONERO_WALLET_RPC_URL') ?: MoneroExplorerRegtestFixture::MONERO_WALLET_RPC_URL;

        self::$rawHttpClient = new Client(['timeout' => 30, 'http_errors' => false]);
        self::$explorerApiClient = new ExplorerTools(
            new HttpFactory(),
            new Client(['timeout' => 30]),
            self::$explorerUrl
        );

        try {
            self::$manifest = MoneroExplorerRegtestFixture::readManifest();
        } catch (Exception $exception) {
            self::fail($exception->getMessage());
        }

        try {
            $height = self::$explorerApiClient->getNetworkInfo()->height;
        } catch (Exception $exception) {
            self::fail(
                'xmrblocks unreachable at ' . self::$explorerUrl
                . '. Run `make integration-up && make integration-seed`. '
                . $exception->getMessage()
            );
        }

        if (
            static::isReadOnlyTestSuite()
            &&
            $height !== MoneroExplorerRegtestFixture::EXPECTED_CHAIN_HEIGHT_AFTER_SEED
        ) {
            self::fail(
                "Explorer height {$height} !== expected "
                . MoneroExplorerRegtestFixture::EXPECTED_CHAIN_HEIGHT_AFTER_SEED
                . '. The chain is unseeded or was mutated. Run '
                . '`make integration-down && make integration-up && make integration-seed`.'
            );
        }
    }

    /**
     * Overridden (to return false) by mutating-state test classes.
     */
    protected static function isReadOnlyTestSuite(): bool
    {
        return true;
    }

    /**
     * JSON-RPC call to monerod or monero-wallet-rpc (mutating tests use this
     * to create transactions and mine blocks).
     *
     * @param array<string,mixed> $params
     * @return array<string,mixed> The `result`.
     */
    protected static function jsonRpc(string $url, string $method, array $params = []): array
    {
        $response = self::$rawHttpClient->post($url . '/json_rpc', [
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
    protected static function pollUntil(callable $condition, int $timeoutSeconds, string $failureMessage): void
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
        self::fail("Timeout after {$timeoutSeconds}s: {$failureMessage}");
    }

    /**
     * xmrblocks indexes the LMDB with a small lag behind monerod; wait for it.
     */
    protected static function waitForExplorerHeight(int $height, int $timeoutSeconds = 60): void
    {
        self::pollUntil(
            fn() => self::$explorerApiClient->getNetworkInfo()->height >= $height,
            $timeoutSeconds,
            "xmrblocks did not index to height {$height}"
        );
    }
}
