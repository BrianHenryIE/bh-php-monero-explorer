<?php

/**
 * Read-only integration tests: every ExplorerApi method against the live,
 * seeded xmrblocks explorer. Nothing here may mutate chain state.
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer;

use BrianHenryIE\MoneroExplorer\Model\Block;
use BrianHenryIE\MoneroExplorer\Model\Transaction;

/**
 * @coversDefaultClass \BrianHenryIE\MoneroExplorer\ExplorerApi
 */
class ExplorerApiIntegrationTest extends MoneroExplorerIntegrationTestCase
{
    public function testGetVersion(): void
    {
        $result = self::$explorerApiClient->getVersion();

        self::assertGreaterThan(0, $result->api);
        self::assertSame(
            MoneroExplorerRegtestFixture::EXPECTED_CHAIN_HEIGHT_AFTER_SEED,
            $result->blockchainHeight
        );
    }

    public function testGetNetworkInfo(): void
    {
        $result = self::$explorerApiClient->getNetworkInfo();

        self::assertSame(MoneroExplorerRegtestFixture::EXPECTED_CHAIN_HEIGHT_AFTER_SEED, $result->height);
        self::assertTrue($result->status);
        self::assertFalse($result->testnet);
        self::assertFalse($result->stagenet);
    }

    public function testGetBlockByHeight(): void
    {
        $result = self::$explorerApiClient->getBlock(self::$manifest['transfers_block_height']);

        self::assertSame(self::$manifest['transfers_block_height'], $result->blockHeight);
        self::assertSame(self::$manifest['transfers_block_hash'], $result->hash);
        // The two seed transfers (the coinbase tx is not included in `txs` by height query... assert presence).
        $txHashes = array_map(fn($blockTx) => $blockTx->txHash, $result->txs);
        self::assertContains(self::$manifest['plain_transfer_txid'], $txHashes);
        self::assertContains(self::$manifest['integrated_transfer_txid'], $txHashes);
    }

    public function testGetBlockByHashRoundTrip(): void
    {
        $result = self::$explorerApiClient->getBlock(self::$manifest['transfers_block_hash']);

        self::assertSame(self::$manifest['transfers_block_height'], $result->blockHeight);
    }

    public function testGetTransactionForThePlainTransfer(): void
    {
        $result = self::$explorerApiClient->getTransaction(self::$manifest['plain_transfer_txid']);

        self::assertSame(self::$manifest['plain_transfer_txid'], $result->txHash);
        self::assertFalse($result->coinbase);
        self::assertSame(self::$manifest['transfers_block_height'], $result->blockHeight);
        self::assertGreaterThanOrEqual(
            MoneroExplorerRegtestFixture::SEED_BLOCKS_MINED_AFTER_TRANSFERS,
            $result->confirmations
        );
        // Modern monero-wallet-rpc embeds a DUMMY encrypted 8-byte payment id
        // (16 hex chars) in every transaction — including plain-address transfers
        // — so paymentId8 is present and indistinguishable from the integrated
        // transfer's real one without the recipient's view key.
        self::assertSame(16, strlen($result->paymentId8));
        self::assertNotEmpty($result->inputs);
        self::assertNotEmpty($result->outputs);
    }

    /**
     * The integrated-address transfer carries an ENCRYPTED 8-byte payment id
     * (16 hex chars); only a party with the view key can decrypt it.
     */
    public function testGetTransactionForTheIntegratedTransferShowsEncryptedPaymentId(): void
    {
        $result = self::$explorerApiClient->getTransaction(self::$manifest['integrated_transfer_txid']);

        self::assertSame(16, strlen($result->paymentId8));
        self::assertSame('', $result->paymentId);
    }

    public function testGetRawTransaction(): void
    {
        $result = self::$explorerApiClient->getRawTransaction(self::$manifest['plain_transfer_txid']);

        self::assertSame(0, $result->unlockTime);
        self::assertSame(2, $result->version);
        self::assertNotEmpty($result->vin);
        self::assertNotEmpty($result->vout);
        self::assertNotEmpty($result->vin[0]->key->kImage);
    }

    public function testGetRawBlock(): void
    {
        $result = self::$explorerApiClient->getRawBlock(self::$manifest['transfers_block_height']);

        self::assertContains(self::$manifest['plain_transfer_txid'], $result->txHashes);
        self::assertContains(self::$manifest['integrated_transfer_txid'], $result->txHashes);
        self::assertNotEmpty($result->minerTx->vout);
    }

    public function testGetDetailedTransaction(): void
    {
        $result = self::$explorerApiClient->getDetailedTransaction(self::$manifest['plain_transfer_txid']);

        // Every scalar in this endpoint's response is wrapped in a single-element
        // array (upstream serializes its template context).
        self::assertSame(self::$manifest['plain_transfer_txid'], $result->txHash[0]);
        self::assertNotEmpty($result->inputs);
        self::assertNotEmpty($result->outputs);
    }

    public function testGetTransactions(): void
    {
        $result = self::$explorerApiClient->getTransactions();

        self::assertSame(MoneroExplorerRegtestFixture::EXPECTED_CHAIN_HEIGHT_AFTER_SEED, $result->currentHeight);
        self::assertNotEmpty($result->blocks);
    }

    public function testGetMempoolIsEmpty(): void
    {
        $result = self::$explorerApiClient->getMempool();

        self::assertSame(0, $result->txsNo);
        self::assertCount(0, $result->txs);
    }

    public function testGetSearchByHeightReturnsBlock(): void
    {
        $result = self::$explorerApiClient->getSearch(self::$manifest['transfers_block_height']);

        self::assertInstanceOf(Block::class, $result);
        self::assertSame('block', $result->title);
        self::assertSame(self::$manifest['transfers_block_hash'], $result->hash);
    }

    public function testGetSearchByTxidReturnsTransaction(): void
    {
        $result = self::$explorerApiClient->getSearch(self::$manifest['plain_transfer_txid']);

        self::assertInstanceOf(Transaction::class, $result);
        self::assertSame('transaction', $result->title);
        self::assertSame(self::$manifest['plain_transfer_txid'], $result->txHash);
    }

    public function testGetEmission(): void
    {
        $result = self::$explorerApiClient->getEmission();

        self::assertGreaterThan(0, $result->blkNo);
        self::assertIsNumeric($result->coinbase);
        self::assertGreaterThan(0, (float) $result->coinbase);
        self::assertIsNumeric($result->fee);
    }

    /**
     * The crown jewel: with the recipient's address + committed view key, the
     * explorer decodes which outputs of the seed transfer belong to the
     * recipient — proving RECEIPT of exactly 1.23 XMR.
     */
    public function testGetOutputsDecodesTheReceivedAmountWithTheViewKey(): void
    {
        $result = self::$explorerApiClient->getOutputs(
            self::$manifest['plain_transfer_txid'],
            MoneroExplorerRegtestFixture::RECIPIENT_WALLET_PRIMARY_ADDRESS,
            MoneroExplorerRegtestFixture::RECIPIENT_WALLET_SECRET_VIEW_KEY,
            false
        );

        self::assertSame(self::$manifest['plain_transfer_txid'], $result->txHash);
        self::assertFalse($result->txProve);

        $matchedAtomicUnits = 0;
        foreach ($result->outputs as $output) {
            if ($output->match) {
                $matchedAtomicUnits += $output->amount;
            }
        }
        self::assertSame(MoneroExplorerRegtestFixture::PLAIN_TRANSFER_AMOUNT_ATOMIC_UNITS, $matchedAtomicUnits);
    }

    /**
     * With `txprove=1` and the TX KEY (not the view key), the SENDER proves
     * they paid the recipient — the other direction of proof.
     */
    public function testGetOutputsWithTxProveProvesTheSentPayment(): void
    {
        $result = self::$explorerApiClient->getOutputs(
            self::$manifest['plain_transfer_txid'],
            MoneroExplorerRegtestFixture::RECIPIENT_WALLET_PRIMARY_ADDRESS,
            self::$manifest['plain_transfer_tx_key'],
            true
        );

        self::assertTrue($result->txProve);

        $matchedAtomicUnits = 0;
        foreach ($result->outputs as $output) {
            if ($output->match) {
                $matchedAtomicUnits += $output->amount;
            }
        }
        self::assertSame(MoneroExplorerRegtestFixture::PLAIN_TRANSFER_AMOUNT_ATOMIC_UNITS, $matchedAtomicUnits);
    }

    /**
     * The last blocks of the seeded chain are coinbase-to-miner, so scanning
     * recent blocks with the MINER's view key finds outputs; the recipient's
     * transfers are deeper than the 5-block scan window and yield none.
     */
    public function testGetOutputsBlocks(): void
    {
        $minerResult = self::$explorerApiClient->getOutputsBlocks(
            MoneroExplorerRegtestFixture::MINER_WALLET_PRIMARY_ADDRESS,
            MoneroExplorerRegtestFixture::MINER_WALLET_SECRET_VIEW_KEY,
            5,
            false
        );

        self::assertNotEmpty($minerResult->outputs);
        // The endpoint's `height` is the tip block NUMBER (chain height − 1).
        self::assertSame(MoneroExplorerRegtestFixture::EXPECTED_CHAIN_HEIGHT_AFTER_SEED - 1, $minerResult->height);

        $recipientResult = self::$explorerApiClient->getOutputsBlocks(
            MoneroExplorerRegtestFixture::RECIPIENT_WALLET_PRIMARY_ADDRESS,
            MoneroExplorerRegtestFixture::RECIPIENT_WALLET_SECRET_VIEW_KEY,
            5,
            false
        );

        self::assertCount(0, $recipientResult->outputs);
    }

    public function testExplorerToolsGetLastBlockHeight(): void
    {
        $result = self::$explorerApiClient->getLastBlockHeight();

        self::assertSame(MoneroExplorerRegtestFixture::EXPECTED_CHAIN_HEIGHT_AFTER_SEED - 1, $result);
    }

    public function testExplorerToolsIsBlockContainsPayment(): void
    {
        self::assertTrue(
            self::$explorerApiClient->isBlockContainsPayment(
                self::$manifest['transfers_block_height'],
                MoneroExplorerRegtestFixture::RECIPIENT_WALLET_PRIMARY_ADDRESS,
                MoneroExplorerRegtestFixture::RECIPIENT_WALLET_SECRET_VIEW_KEY
            )
        );

        // A coinbase-only block (mined before the transfers) contains no payment
        // to the recipient.
        self::assertFalse(
            self::$explorerApiClient->isBlockContainsPayment(
                50,
                MoneroExplorerRegtestFixture::RECIPIENT_WALLET_PRIMARY_ADDRESS,
                MoneroExplorerRegtestFixture::RECIPIENT_WALLET_SECRET_VIEW_KEY
            )
        );
    }
}
