<?php

namespace BrianHenryIE\MoneroExplorer;

use BrianHenryIE\MoneroExplorer\Model\BlockTx;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BrianHenryIE\MoneroExplorer\ExplorerTools
 */
class ExplorerToolsContractTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $env = parse_ini_file(__DIR__ . '/../../.env.secret');
        foreach ($env as $item => $value) {
            if (str_starts_with($value, '#')) {
                continue;
            }
            $_ENV[ $item ] = $value;
        }
    }

    /**
     * @cover ::getTxsFromBlock
     */
    public function testGetTransactionsFromHeight(): void
    {
        $secret_viewkey = $_ENV['SAMPLE_TRANSACTION_SECRET_VIEW_KEY'];

        $received_address = $_ENV['SAMPLE_TRANSACTION_RECEIVED_ADDRESS'];
        $transaction_id   = $_ENV['SAMPLE_TRANSACTION_TRANSACTION_ID'];
        $block_height     = (int) $_ENV['SAMPLE_TRANSACTION_TX_BLOCK_HEIGHT'];

        $requestFactory = new HttpFactory();
        $client = new Client();

        $sut = new ExplorerTools($requestFactory, $client);

        $valid = array();

        $result = $sut->getBlock($block_height)->getTxs();

        $tx_hashes = array_map(
            function (BlockTx $transaction) {
                return $transaction->getTxHash();
            },
            $result
        );

        foreach ($tx_hashes as $tx_hash) {
            $outputsData = $sut->getOutputs($tx_hash, $received_address, $secret_viewkey);

            $outputs = $outputsData->getOutputs();
            foreach ($outputs as $output) {
                if ($output->isMatch()) {
                    $valid[ $tx_hash ] = $output->getAmount() / 1000000000000;
                }
            }
        }

        self::assertArrayHasKey($transaction_id, $valid);
        self::assertEquals(0.240718739747, $valid[ $transaction_id ]);
    }

    /**
     * @covers ::verifyPaymentInMempool
     */
    public function testVerifyPaymentInMempool(): void
    {

        self::markTestIncomplete('Need to send a payment and capture response');

        $payment_id = $_ENV['SAMPLE_TRANSACTION_TRANSACTION_ID'];
        $payment_address = $_ENV['SAMPLE_TRANSACTION_RECEIVED_ADDRESS'];
        $viewkey = $_ENV['SAMPLE_TRANSACTION_SECRET_VIEW_KEY'];

        $requestFactory = new HttpFactory();
        $client = new Client();

        $sut = new ExplorerTools($requestFactory, $client);

        $result = $sut->verifyPaymentInMempool($payment_id, $payment_address, $viewkey);
    }
}
