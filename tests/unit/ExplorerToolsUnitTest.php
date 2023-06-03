<?php

namespace BrianHenryIE\MoneroExplorer;

use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @coversDefaultClass \BrianHenryIE\MoneroExplorer\ExplorerTools
 */
class ExplorerToolsUnitTest extends TestCase
{
    /**
     * @covers ::getLastBlockHeight
     */
    public function testGetLastBlockHeight(): void
    {

        global $project_root_dir;
        $response_json = file_get_contents(__DIR__ . '/../_data/explorer-tools/getLastBlockHeight.json');

        $response_stream = Mockery::mock(StreamInterface::class);
        $response_stream->expects()->__toString()->andReturn($response_json);

        $response = Mockery::mock(ResponseInterface::class);
        $response->expects()->getBody()->andReturn($response_stream);

        $client = Mockery::mock(ClientInterface::class);
        $client->expects()->sendRequest(Mockery::any())->andReturn($response);

        $requestFactory = Mockery::mock(RequestFactoryInterface::class);
        $requestFactory->expects()->createRequest('GET', Mockery::any());

        $sut = new ExplorerTools($requestFactory, $client);

        $result = $sut->getLastBlockHeight();

        $this->assertEquals(2786651, $result);
    }

    /**
     * @covers ::getMempool
     */
    public function testGetMempoolTxs(): void
    {
        $response_json = file_get_contents(__DIR__ . '/../_data/explorer-tools/getMempoolTxs.json');

        $response_stream = Mockery::mock(StreamInterface::class)->makePartial();
        $response_stream->expects()->__toString()->andReturn($response_json);

        $response = Mockery::mock(ResponseInterface::class)->makePartial();
        $response->expects()->getBody()->andReturn($response_stream);

        $client = Mockery::mock(ClientInterface::class)->makePartial();
        $client->expects()->sendRequest(Mockery::any())->andReturn($response);

        $requestFactory = Mockery::mock(RequestFactoryInterface::class);
        $requestFactory->expects()->createRequest('GET', Mockery::any());

        $sut = new ExplorerTools($requestFactory, $client);

        $result = $sut->getMempool()->getTxs();

        $this->assertCount(73, $result);
    }

    /**
     * @covers ::getLastBlockHeight
     */
    public function testGetLastBlockHeightOOOOOTOO(): void
    {
        $response_json = file_get_contents(__DIR__ . '/../_data/explorer-tools/getLastBlockHeight.json');

        $response_stream = Mockery::mock(StreamInterface::class)->makePartial();
        $response_stream->expects()->__toString()->andReturn($response_json);

        $response = Mockery::mock(ResponseInterface::class)->makePartial();
        $response->expects()->getBody()->andReturn($response_stream);

        $client = Mockery::mock(ClientInterface::class)->makePartial();
        $client->expects()->sendRequest(Mockery::any())->andReturn($response);

        $requestFactory = Mockery::mock(RequestFactoryInterface::class);
        $requestFactory->expects()->createRequest('GET', Mockery::any());

        $sut = new ExplorerTools($requestFactory, $client);

        $result = $sut->getLastBlockHeight();

        self::assertEquals(2786651, $result);
    }
}
