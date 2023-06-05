<?php

/**
 * Tests which run live HTTP calls to xmrchain.net.
 *
 * Sample wallet and transaction need to be configured in `.env.secret` file.
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer;

use BrianHenryIE\MoneroExplorer\Model\Block;
use BrianHenryIE\MoneroExplorer\Model\Transaction;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \BrianHenryIE\MoneroExplorer\ExplorerApi
 */
class ExplorerApiContractTest extends TestCase
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

    public function testGetTransaction(): void
    {
        $requestFactory = new HttpFactory();
        $client = new Client();

        $sut = new ExplorerApi($requestFactory, $client);

        $transactionId = $_ENV['SAMPLE_TRANSACTION_TRANSACTION_ID'];

        $sut->getTransaction($transactionId);

        self::expectNotToPerformAssertions();
    }

    public function testGetRawTransaction(): void
    {
        $requestFactory = new HttpFactory();
        $client = new Client();

        $sut = new ExplorerApi($requestFactory, $client);

        $transactionId = $_ENV['SAMPLE_TRANSACTION_TRANSACTION_ID'];

        $sut->getRawTransaction($transactionId);

        self::expectNotToPerformAssertions();
    }

    public function testGetDetailedTransaction(): void
    {

        $requestFactory = new HttpFactory();
        $client = new Client();

        $sut = new ExplorerApi($requestFactory, $client);

        $transactionId = $_ENV['SAMPLE_TRANSACTION_TRANSACTION_ID'];

        $sut->getDetailedTransaction($transactionId);

        self::expectNotToPerformAssertions();
    }

    public function testGetBlock(): void
    {
        $requestFactory = new HttpFactory();
        $client = new Client();

        $sut = new ExplorerApi($requestFactory, $client);

        $blockNumber = (int) $_ENV['SAMPLE_TRANSACTION_TX_BLOCK_HEIGHT'];

        $sut->getBlock($blockNumber);

        self::expectNotToPerformAssertions();
    }

    public function testGetRawBlock(): void
    {

        $requestFactory = new HttpFactory();
        $client = new Client();

        $sut = new ExplorerApi($requestFactory, $client);

        $blockNumber = (int) $_ENV['SAMPLE_TRANSACTION_TX_BLOCK_HEIGHT'];

        $sut->getRawBlock($blockNumber);

        self::expectNotToPerformAssertions();
    }


    // TODO: testGetTransactions()

    /**
     * @covers ::getMempool
     */
    public function testGetMempool(): void
    {
        $requestFactory = new HttpFactory();
        $client = new Client();

        $sut = new ExplorerApi($requestFactory, $client);

        $sut->getMempool();

        self::expectNotToPerformAssertions();
    }

    public function testGetSearchTransactionHash(): void
    {
        $requestFactory = new HttpFactory();
        $client = new Client();

        $sut = new ExplorerApi($requestFactory, $client);

        $result = $sut->getSearch('2773f57e0b8355e4eb1c8578a860ae8fa18ba04ce287f317b9ac015d42e3ee24');

        self::assertInstanceOf(Transaction::class, $result);
    }

    public function testGetSearchBlockHash(): void
    {
        $requestFactory = new HttpFactory();
        $client = new Client();

        $sut = new ExplorerApi($requestFactory, $client);

        $result = $sut->getSearch('6370f4fea197d2537585ab88e4b7b3ee2a7024daef211c71ddea39f0eb789f8c');

        self::assertInstanceOf(Block::class, $result);
    }

    public function testGetSearchBlockNumber(): void
    {
        $requestFactory = new HttpFactory();
        $client = new Client();

        $sut = new ExplorerApi($requestFactory, $client);

        $result = $sut->getSearch('2676047');

        self::assertInstanceOf(Block::class, $result);
    }

    public function testGetNetworkInfoBlockNumber(): void
    {
        $requestFactory = new HttpFactory();
        $client = new Client();

        $sut = new ExplorerApi($requestFactory, $client);

        $result = $sut->getNetworkInfo();

        self::expectNotToPerformAssertions();
    }

    public function testGetEmission(): void
    {
        $requestFactory = new HttpFactory();
        $client = new Client();

        $sut = new ExplorerApi($requestFactory, $client);

        $sut->getEmission();

        self::expectNotToPerformAssertions();
    }


    /**
     * @covers ::getOutputs
     */
    public function testGetOutputs(): void
    {
        $transactionHash = $_ENV['SAMPLE_TRANSACTION_TRANSACTION_ID'];
        $primaryAddress = $_ENV['SAMPLE_PRIMARY_ADDRESS'];
        $publicViewkey = $_ENV['SAMPLE_PUBLIC_VIEW_KEY'];

        $requestFactory = new HttpFactory();
        $client = new Client();

        $sut = new ExplorerApi($requestFactory, $client);

        $sut->getOutputs($transactionHash, $primaryAddress, $publicViewkey);

        self::expectNotToPerformAssertions();
    }

    /**
     * @covers ::getOutputsBlocks
     *
     * TODO: Test supplying a negative limit. I presume the minimum is 1.
     */
    public function testGetOutputsBlocks(): void
    {
        $this->markTestIncomplete('OutputsBlocks object is not yet implemented fully');

        $primary_address = $_ENV['SAMPLE_PRIMARY_ADDRESS'];
        $public_viewkey = $_ENV['SAMPLE_PUBLIC_VIEW_KEY'];

        $requestFactory = new HttpFactory();
        $client = new Client();

        $sut = new ExplorerApi($requestFactory, $client);

        $sut->getOutputsBlocks($primary_address, $public_viewkey);

        self::expectNotToPerformAssertions();
    }

    public function testGetVersion(): void
    {
        $requestFactory = new HttpFactory();
        $client = new Client();

        $sut = new ExplorerApi($requestFactory, $client);

        $sut->getVersion();

        self::expectNotToPerformAssertions();
    }
}
