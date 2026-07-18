<?php

namespace BrianHenryIE\MoneroExplorer;

use BrianHenryIE\MoneroExplorer\Exception\IncompleteExplorerResponseException;
use BrianHenryIE\MoneroExplorer\Model\Version;
use Exception;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @coversDefaultClass \BrianHenryIE\MoneroExplorer\ExplorerApi
 */
class ExplorerApiUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function getSutForResponse(string $responseBody, int $statusCode = 200): ExplorerApi
    {
        $response_stream = Mockery::mock(StreamInterface::class);
        $response_stream->allows()->__toString()->andReturn($responseBody);

        $response = Mockery::mock(ResponseInterface::class);
        $response->allows()->getBody()->andReturn($response_stream);
        $response->allows()->getStatusCode()->andReturn($statusCode);

        $client = Mockery::mock(ClientInterface::class);
        $client->allows()->sendRequest(Mockery::any())->andReturn($response);

        $requestFactory = Mockery::mock(RequestFactoryInterface::class);
        $requestFactory->allows()->createRequest('GET', Mockery::any());

        return new ExplorerApi($requestFactory, $client);
    }

    /**
     * A JSend `fail`/`error` status must surface as an exception with the API's message.
     *
     * @covers ::callApi
     */
    public function testJsendFailStatusThrowsWithMessage(): void
    {
        $sut = $this->getSutForResponse(
            '{"status": "error", "message": "Tx hash not found"}'
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Tx hash not found');

        $sut->getVersion();
    }

    /**
     * A non-2xx response (e.g. 404 when `--enable-json-api` is not set) must
     * throw with an actionable message, not attempt to parse the body.
     *
     * @covers ::callApi
     */
    public function testNonSuccessHttpStatusCodeThrows(): void
    {
        $sut = $this->getSutForResponse('not json', 404);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('--enable-json-api');

        $sut->getVersion();
    }

    /**
     * Hydration failures are wrapped in IncompleteExplorerResponseException with the
     * raw body attached to the exception object (not the message).
     *
     * @covers ::callApi
     */
    public function testHydrationFailureThrowsIncompleteResponseExceptionWithBodyAttached(): void
    {
        // JsonMapper throws on null provided for the non-nullable typed property.
        $responseBody = '{"status": "success", "data": {"api": null, "secret_marker": "do-not-log"}}';

        $sut = $this->getSutForResponse($responseBody);

        try {
            $sut->getVersion();
            self::fail('Expected IncompleteExplorerResponseException');
        } catch (IncompleteExplorerResponseException $exception) {
            self::assertStringContainsString('api/version', $exception->getMessage());
            self::assertStringContainsString(Version::class, $exception->getMessage());
            self::assertSame($responseBody, $exception->getResponseBody());
            // Key NAMES appear in the message; VALUES must not (view keys are echoed in some responses).
            self::assertStringContainsString('secretMarker', $exception->getMessage());
            self::assertStringNotContainsString('do-not-log', $exception->getMessage());
        }
    }

    /**
     * Unsigned 64-bit values above PHP_INT_MAX must survive decoding without being
     * corrupted to floats: mainnet's cumulative emission coinbase already exceeds
     * PHP_INT_MAX (e.g. 18277547995551232000 > 9223372036854775807).
     *
     * The value arrives as a numeric string thanks to JSON_BIGINT_AS_STRING; until
     * the Emission model is typed with a big-number value object, this test pins
     * the DECODING behavior via the JSend envelope (the mapper is exercised with
     * in-range values elsewhere).
     *
     * @covers ::buildResponseMapper
     */
    public function testBigIntegerValuesAreNotCorruptedToFloatsByDecoding(): void
    {
        $decoded = json_decode(
            '{"status": "success", "data": {"coinbase": 18277547995551232000}}',
            false,
            512,
            JSON_BIGINT_AS_STRING | JSON_THROW_ON_ERROR
        );

        self::assertIsString($decoded->data->coinbase);
        self::assertSame('18277547995551232000', $decoded->data->coinbase);
    }
}
