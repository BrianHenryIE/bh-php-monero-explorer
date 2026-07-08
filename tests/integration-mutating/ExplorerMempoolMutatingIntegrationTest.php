<?php

/**
 * Integration tests which mutate chain state (create transactions, mine
 * blocks) to exercise the explorer's mempool-related behavior. Runs AFTER the
 * read-only `integration` suite; expectations are re-derived from live reads.
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer;

/**
 * @coversDefaultClass \BrianHenryIE\MoneroExplorer\ExplorerApi
 */
class ExplorerMempoolMutatingIntegrationTest extends MoneroExplorerIntegrationTestCase
{
    protected static function isReadOnlyTestSuite(): bool
    {
        return false;
    }

    protected static function openMinerWallet(): void
    {
        self::jsonRpc(self::$walletRpcUrl, 'open_wallet', [
            'filename' => MoneroExplorerRegtestFixture::MINER_WALLET_FILENAME,
            'password' => MoneroExplorerRegtestFixture::MINER_WALLET_PASSWORD,
        ]);
        self::jsonRpc(self::$walletRpcUrl, 'refresh');
    }

    protected static function mineBlocksAndWaitForExplorer(int $blocks): void
    {
        $heightBefore = (int) self::jsonRpc(self::$daemonRpcUrl, 'get_block_count')['count'];
        self::jsonRpc(self::$daemonRpcUrl, 'generateblocks', [
            'amount_of_blocks' => $blocks,
            'wallet_address' => MoneroExplorerRegtestFixture::MINER_WALLET_PRIMARY_ADDRESS,
            'starting_nonce' => 0,
        ]);
        self::waitForExplorerHeight($heightBefore + $blocks, 120);
    }

    /**
     * An unconfirmed transfer to the recipient's INTEGRATED address must be
     * visible in the explorer's mempool, findable by
     * ExplorerTools::verifyPaymentInMempool() via the fixed payment id, and by
     * getOutputsBlocks(mempool: true) via the view key.
     */
    public function testUnconfirmedIntegratedAddressPaymentIsFoundInMempool(): void
    {
        self::openMinerWallet();

        $transferResult = self::jsonRpc(self::$walletRpcUrl, 'transfer', [
            'destinations' => [[
                'amount' => 250000000000, // 0.25 XMR.
                'address' => MoneroExplorerRegtestFixture::RECIPIENT_WALLET_INTEGRATED_ADDRESS,
            ]],
        ]);
        $unconfirmedTxid = $transferResult['tx_hash'];

        try {
            self::pollUntil(
                fn() => self::$explorerApiClient->getMempool()->txsNo > 0,
                60,
                'The unconfirmed transaction did not appear in the explorer mempool'
            );

            $mempool = self::$explorerApiClient->getMempool();
            $mempoolTxHashes = array_map(fn($mempoolTx) => $mempoolTx->txHash, $mempool->txs);
            self::assertContains($unconfirmedTxid, $mempoolTxHashes);

            $paymentsFound = self::$explorerApiClient->verifyPaymentInMempool(
                MoneroExplorerRegtestFixture::SEED_PAYMENT_ID,
                MoneroExplorerRegtestFixture::RECIPIENT_WALLET_PRIMARY_ADDRESS,
                MoneroExplorerRegtestFixture::RECIPIENT_WALLET_SECRET_VIEW_KEY
            );
            self::assertNotEmpty($paymentsFound);
            self::assertSame(250000000000, $paymentsFound[0]['amount']);
            self::assertSame($unconfirmedTxid, $paymentsFound[0]['tx_id']);

            $outputsBlocksResult = self::$explorerApiClient->getOutputsBlocks(
                MoneroExplorerRegtestFixture::RECIPIENT_WALLET_PRIMARY_ADDRESS,
                MoneroExplorerRegtestFixture::RECIPIENT_WALLET_SECRET_VIEW_KEY,
                5,
                true
            );
            $inMempoolOutputs = array_filter(
                $outputsBlocksResult->outputs,
                fn($output) => $output->inMempool
            );
            self::assertNotEmpty($inMempoolOutputs);
        } finally {
            // Confirm the transaction so later runs/suites start with an empty pool.
            self::mineBlocksAndWaitForExplorer(10);
        }

        $mempoolAfterMining = self::$explorerApiClient->getMempool();
        self::assertSame(0, $mempoolAfterMining->txsNo);
    }
}
