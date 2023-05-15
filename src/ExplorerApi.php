<?php

/**
 * Client for Onion Monero Blockchain Explorer HTTP API, aka xmrblocks.
 * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/
 *
 * Provides a one-to-one function map to the API methods:
 * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/aa96ce2927c050fabe17154a3bdfb09be83a632f/main.cpp#L656-L837
 *
 * Public API implementation:
 * @see https://xmrchain.net/
 *
 * Example API requests:
 * @see https://explorer.ryo-currency.com/api
 *
 * Monero official website:
 * @see https://www.getmonero.org/
 *
 * @author Serhack
 * @author cryptochangements
 * @author mosu-forge
 * @author BrianHenryIE
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer;

use BrianHenryIE\MoneroExplorer\Model\Block;
use BrianHenryIE\MoneroExplorer\Model\DetailedTransaction;
use BrianHenryIE\MoneroExplorer\Model\Emission;
use BrianHenryIE\MoneroExplorer\Model\JsonMapper\BlockMapper;
use BrianHenryIE\MoneroExplorer\Model\JsonMapper\DetailedTransactionMapper;
use BrianHenryIE\MoneroExplorer\Model\JsonMapper\EmissionMapper;
use BrianHenryIE\MoneroExplorer\Model\JsonMapper\MempoolMapper;
use BrianHenryIE\MoneroExplorer\Model\JsonMapper\NetworkInfoMapper;
use BrianHenryIE\MoneroExplorer\Model\JsonMapper\OutputsBlocksMapper;
use BrianHenryIE\MoneroExplorer\Model\JsonMapper\OutputsMapper;
use BrianHenryIE\MoneroExplorer\Model\JsonMapper\RawBlockMapper;
use BrianHenryIE\MoneroExplorer\Model\JsonMapper\RawTransactionMapper;
use BrianHenryIE\MoneroExplorer\Model\JsonMapper\SearchMapper;
use BrianHenryIE\MoneroExplorer\Model\JsonMapper\TransactionMapper;
use BrianHenryIE\MoneroExplorer\Model\JsonMapper\VersionMapper;
use BrianHenryIE\MoneroExplorer\Model\Mempool;
use BrianHenryIE\MoneroExplorer\Model\NetworkInfo;
use BrianHenryIE\MoneroExplorer\Model\Outputs;
use BrianHenryIE\MoneroExplorer\Model\OutputsBlocks;
use BrianHenryIE\MoneroExplorer\Model\RawBlock;
use BrianHenryIE\MoneroExplorer\Model\RawTransaction;
use BrianHenryIE\MoneroExplorer\Model\Search;
use BrianHenryIE\MoneroExplorer\Model\Transaction;
use BrianHenryIE\MoneroExplorer\Model\Version;
use Exception;
use JsonException;
use JsonMapper\JsonMapperFactory;
use miWebb\JSend\JSend;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use UnexpectedValueException;

/**
 * All API requests are GET.
 *
 * Use `sprintf()` to interpolate the path and querystring parameters.
 *
 * @throws ClientExceptionInterface
 * @throws JsonException
 */
class ExplorerApi
{
    /**
     * PSR HTTP implementation.
     */
    protected RequestFactoryInterface $requestFactory;

    /**
     * PSR HTTP client for making requests.
     */
    protected ClientInterface $client;

    /**
     * The server to use, typically `https://xmrchain.net`.
     *
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer#explorer-hosts
     *
     * @see ExplorerTools::MAINNET_URL
     * @see ExplorerTools::TESTNET_URL
     */
    protected string $url;

    /**
     * Constructor
     *
     * @param RequestFactoryInterface $requestFactory
     * @param ClientInterface $client A PSR HTTP client.
     * @param string            $url The server to query.
     */
    public function __construct(RequestFactoryInterface $requestFactory, ClientInterface $client, string $url = ExplorerTools::MAINNET_URL)
    {
        $this->url = $url;
        $this->client = $client;
        $this->requestFactory = $requestFactory;
    }

    /**
     *
     *
     * `/api/transaction/<string>`
     * TODO: cURL
     * @see Transaction
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/aa96ce2927c050fabe17154a3bdfb09be83a632f/main.cpp#L661-L667C12
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/page.h#L4395-L4587
     *
     * @param string $txHash
     */
    public function getTransaction(string $txHash): Transaction
    {
        $endpoint = sprintf(
            'transaction/%s',
            $txHash
        );

        return $this->callApi($endpoint, TransactionMapper::class);
    }

    /**
     *
     *
     * `/api/rawtransaction/<txhash>`
     * TODO: cURL
     * @see RawTransaction
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/aa96ce2927c050fabe17154a3bdfb09be83a632f/main.cpp#L669-L675
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/page.h#L4591-L4672
     *
     * @param string $txHash The transaction id.
     */
    public function getRawTransaction(string $txHash): RawTransaction
    {
        $endpoint = sprintf(
            'rawtransaction/%s',
            $txHash
        );

        return $this->callApi($endpoint, RawTransactionMapper::class);
    }

    /**
     *
     *
     * `/api/detailedtransaction/<string>`
     * TODO: cURL
     * @see DetailedTransaction
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/aa96ce2927c050fabe17154a3bdfb09be83a632f/main.cpp#L677-L683
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/page.h#L4675-L4722
     *
     * @param string $txHash
     */
    public function getDetailedTransaction(string $txHash): DetailedTransaction
    {
        $endpoint = sprintf(
            'detailedtransaction/%s',
            $txHash
        );

        return $this->callApi($endpoint, DetailedTransactionMapper::class);
    }

    /**
     *
     *
     * `/api/block/<block-or-hash>`
     * TODO: cURL
     * @see Block
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/aa96ce2927c050fabe17154a3bdfb09be83a632f/main.cpp#L685-L691
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/page.h#L4724-L4863
     *
     * @param int|string $blockOrHash
     */
    public function getBlock($blockOrHash): Block
    {
        if (! (is_int($blockOrHash) || is_string($blockOrHash))) {
            throw new \InvalidArgumentException();
        }

        $endpoint = sprintf(
            'block/%s',
            (string) $blockOrHash
        );

        return $this->callApi($endpoint, BlockMapper::class);
    }

    /**
     *
     *
     * `/api/rawblock/<string>`
     * TODO: cURL
     * @see RawBlock
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/aa96ce2927c050fabe17154a3bdfb09be83a632f/main.cpp#L693-L699
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/page.h#L4867-L4960
     *
     * @param int|string $blockOrHash
     */
    public function getRawBlock($blockOrHash): RawBlock
    {
        if (! (is_int($blockOrHash) || is_string($blockOrHash))) {
            throw new \InvalidArgumentException();
        }
        $endpoint = sprintf(
            'rawblock/%s',
            (string) $blockOrHash
        );

        return $this->callApi($endpoint, RawBlockMapper::class);
    }

    /**
     *
     * `/api/transactions?page=<number>&limit=<quantity>`
     * `curl "https://xmrchain.net/api/transactions?page=2&limit=5" | jq`
     * @see Transactions
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/aa96ce2927c050fabe17154a3bdfb09be83a632f/main.cpp#L701-L714
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/page.h#L4963-L5084
     *
     * @param int $page
     * @param int $limit
     */
    public function getTransactions(int $page = 0, int $limit = 25): Transactions
    {
        throw new Exception('Not yet implemented');

        $endpoint = sprintf(
            'transactions?page=%d&limit=%d',
            $page,
            $limit
        );

        return $this->callApi($endpoint, TransactionMapper::class);
    }

    /**
     *
     * `/api/mempool?page=<number>&limit=<quantity>`
     * `curl https://xmrchain.net/api/mempool | jq`
     * @see Mempool
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/aa96ce2927c050fabe17154a3bdfb09be83a632f/main.cpp#L716-L732
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/page.h#L5087-L5188
     *
     * @param int $page
     * @param int $limit
     */
    public function getMempool(int $page = 0, int $limit = 100000000): Mempool
    {
        $endpoint = sprintf(
            'mempool?page=%d&limit=%d',
            $page,
            $limit
        );

        return $this->callApi($endpoint, MempoolMapper::class);
    }

    /**
     *
     * `api/search/<query>`
     * `curl https://xmrchain.net/api/search/12345 | jq`
     * @see Search
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/aa96ce2927c050fabe17154a3bdfb09be83a632f/main.cpp#L734-L740
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/page.h#L5191-L5239
     *
     * @param string|int $value Transaction hash | block hash | block number.
     */
    public function getSearch($value): Search
    {
        throw new Exception('Not yet implemented');

        if (! (is_int($value) || is_string($value))) {
            throw new \InvalidArgumentException();
        }

        $endpoint = sprintf(
            'search/%s',
            (string) $value
        );

        return $this->callApi($endpoint, SearchMapper::class);
    }

    /**
     * Get the information about the Monero network, e.g. block height, hash rate...
     *
     * `/api/networkinfo`
     * `curl https://xmrchain.net/api/networkinfo | jq`
     * @see NetworkInfo
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/aa96ce2927c050fabe17154a3bdfb09be83a632f/main.cpp#L742-L748
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/page.h#L5674-L5730
     */
    public function getNetworkInfo(): NetworkInfo
    {
        return $this->callApi('networkinfo', NetworkInfoMapper::class);
    }

    /**
     *
     * NB: Emission is disabled by default.
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer#enable-monero-emission
     *
     * `/api/emission`
     * `curl https://xmrchain.net/api/emission | jq`
     * @see Emission
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/aa96ce2927c050fabe17154a3bdfb09be83a632f/main.cpp#L750-L756
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/page.h#L5733-L5774
     */
    public function getEmission(): Emission
    {
        return $this->callApi('emission', EmissionMapper::class);
    }

    /**
     * Get transaction outputs
     *
     * `/api/outputs?txhash=%s&address=%s&viewkey=%s&txprove=%d`
     * TODO: cURL
     * @see Outputs
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/aa96ce2927c050fabe17154a3bdfb09be83a632f/main.cpp#L758-L790
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/page.h#L5260-L5488
     *
     * @param string $txHash
     * @param string $paymentAddress
     * @param string $viewkey
     * @param bool $txProve
     *
     */
    public function getOutputs(string $txHash, string $paymentAddress, string $viewkey, bool $txProve = false): Outputs
    {
        $endpoint = sprintf(
            'outputs?txhash=%s&address=%s&viewkey=%s&txprove=%d',
            $txHash,
            $paymentAddress,
            $viewkey,
            (int) $txProve
        );

        return $this->callApi($endpoint, OutputsMapper::class);
    }

    /**
     * Search for our outputs in last few blocks (up to 5 blocks), using provided address and viewkey.
     *
     * `/api/outputsblocks?address=<address>&viewkey=<viewkey>&limit=<1-5>&mempool=<0|1>>`.
     * TODO: cURL
     * @see OutputsBlocks
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/aa96ce2927c050fabe17154a3bdfb09be83a632f/main.cpp#L792-L827
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/page.h#L5492-L5672
     *
     * @param string $address
     * @param string $viewkey
     * @param int<0,5> $limit
     * @param bool   $mempool
     */
    public function getOutputsBlocks(string $address, string $viewkey, int $limit = 5, bool $mempool = true): OutputsBlocks
    {
        throw new Exception('Not yet implemented');

        $endpoint = sprintf(
            'outputsblocks?address=%s&viewkey=%s&limit=%d&mempool=%d',
            $address,
            $viewkey,
            min($limit, 5),
            (int) $mempool
        );

        return $this->callApi($endpoint, OutputsBlocksMapper::class);
    }

    /**
     *
     *
     * `/api/version`
     * `curl https://xmrchain.net/api/version | jq`
     * @see Version
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/aa96ce2927c050fabe17154a3bdfb09be83a632f/main.cpp#L829-L835
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/page.h#L5777-L5803
     */
    public function getVersion(): Version
    {
        return $this->callApi('version', VersionMapper::class);
    }

    /**
     * Queries the API via PSR client, parses the JSend and casts the value to an object.
     *
     * API responses are JSON encoded JSend compliant messages. The `data` key is returned as an associative array,
     * or an exception thrown.
     *
     * @see https://github.com/omniti-labs/jsend
     *
     * @template T
     * @param string $endpoint The REST route, excluding the domain.
     * @param class-string<T> $type The object type to cast/deserialize the response to.
     * @returns T
     *
     * @throws ClientExceptionInterface PSR HTTP client exception.
     * @throws JsonException
     * @throws UnexpectedValueException If the HTTP server returns a format not compliant with JSend.
     * @throws Exception
     */
    protected function callApi(string $endpoint, string $type)
    {
        if (! class_exists($type)) {
            throw new UnexpectedValueException("{$type} class does not exist");
        }

        $request = $this->requestFactory->createRequest('GET', "{$this->url}/api/$endpoint");

        $response = $this->client->sendRequest($request);

        $jsend = JSend::decode($response->getBody());

        if ($jsend->getStatus() !== JSend::SUCCESS) {
            throw new Exception('API: ' . $jsend->getStatus() . ( $jsend->getMessage() ? ": {$jsend->getMessage()}" : '' ));
        }

        $mapper = (new JsonMapperFactory())->bestFit();

        $mapper->push(new \JsonMapper\Middleware\CaseConversion(
            \JsonMapper\Enums\TextNotation::UNDERSCORE(),
            \JsonMapper\Enums\TextNotation::CAMEL_CASE()
        ));

        return $mapper->mapToClass(json_decode(json_encode($jsend->getData())), $type);
    }
}
