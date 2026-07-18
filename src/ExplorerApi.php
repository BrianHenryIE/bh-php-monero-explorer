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
use BrianHenryIE\MoneroExplorer\Model\Mempool;
use BrianHenryIE\MoneroExplorer\Model\NetworkInfo;
use BrianHenryIE\MoneroExplorer\Model\Outputs;
use BrianHenryIE\MoneroExplorer\Model\OutputsBlocks;
use BrianHenryIE\MoneroExplorer\Model\RawBlock;
use BrianHenryIE\MoneroExplorer\Model\RawTransaction;
use BrianHenryIE\MoneroExplorer\Model\Transaction;
use BrianHenryIE\MoneroExplorer\Model\Transactions;
use BrianHenryIE\MoneroExplorer\Model\Version;
use Exception;
use JsonException;
use JsonMapper\Enums\TextNotation;
use BrianHenryIE\MoneroExplorer\Exception\IncompleteExplorerResponseException;
use JsonMapper\Handler\FactoryRegistry;
use JsonMapper\Handler\PropertyMapper;
use JsonMapper\JsonMapperBuilder;
use JsonMapper\JsonMapperInterface;
use stdClass;
use Throwable;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use UnexpectedValueException;

/**
 * All API requests are GET.
 *
 * Use `sprintf()` to interpolate the path and querystring parameters.
 *
 * WHAT A BLOCK EXPLORER IS: a Monero NODE (monerod) validates and stores the
 * chain; an EXPLORER like xmrblocks is an indexer over that node's public
 * chain data, exposing it over HTTP. Everything an explorer serves is public
 * information — but Monero hides recipients (stealth addresses), amounts
 * (RingCT), and the real spent output in each ring, so "public" reveals far
 * less than on transparent chains. The `outputs`/`outputsblocks` endpoints
 * can decode more, but only when YOU supply a view key.
 *
 * TRUST MODEL: an explorer can lie or be compromised. Statements that matter
 * (a payment was received/sent) are independently verifiable: view-key output
 * decoding and `txprove` are cryptographic checks any instance — including
 * your own (see docker-compose.yml in this repository) — will reproduce.
 *
 * RESPONSE ENVELOPE: all responses are JSend — `{"status": "success", "data": {...}}`,
 * or `status` of `fail`/`error` with a `message` (surfaced as exceptions here).
 *
 * @see https://github.com/omniti-labs/jsend
 * @see https://www.getmonero.org/resources/moneropedia/
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
    public function __construct(
        RequestFactoryInterface $requestFactory,
        ClientInterface $client,
        string $url = ExplorerTools::MAINNET_URL,
    ) {
        $this->url = $url;
        $this->client = $client;
        $this->requestFactory = $requestFactory;
    }

    /**
     * Fetch a transaction by its hash, as interpreted/indexed data.
     *
     * `/api/transaction/<string>`
     * `curl "http://127.0.0.1:8081/api/transaction/<txhash>" | jq`
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

        return $this->callApi($endpoint, Transaction::class);
    }

    /**
     * Fetch a transaction as monerod itself represents it: ring member offsets,
     * key images, RingCT signature data — no interpretation applied.
     *
     * `/api/rawtransaction/<txhash>`
     * `curl "http://127.0.0.1:8081/api/rawtransaction/<txhash>" | jq`
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

        return $this->callApi($endpoint, RawTransaction::class);
    }

    /**
     * Fetch a transaction with per-ring-member detail (ages, timescales) as
     * shown on the explorer's HTML transaction page.
     *
     * NB: the response is upstream's template context — every scalar arrives
     * wrapped in a single-element array. See the model's docblock.
     *
     * `/api/detailedtransaction/<string>`
     * `curl "http://127.0.0.1:8081/api/detailedtransaction/<txhash>" | jq`
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

        return $this->callApi($endpoint, DetailedTransaction::class);
    }

    /**
     * Fetch a block by height or hash, with its transactions summarized.
     *
     * `/api/block/<block-or-hash>`
     * `curl "http://127.0.0.1:8081/api/block/121" | jq`
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

        return $this->callApi($endpoint, Block::class);
    }

    /**
     * Fetch a block as monerod itself represents it (header fields, miner
     * transaction, transaction hashes).
     *
     * `/api/rawblock/<string>`
     * `curl "http://127.0.0.1:8081/api/rawblock/121" | jq`
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

        return $this->callApi($endpoint, RawBlock::class);
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
        $endpoint = sprintf(
            'transactions?page=%d&limit=%d',
            $page,
            $limit
        );

        return $this->callApi($endpoint, Transactions::class);
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

        return $this->callApi($endpoint, Mempool::class);
    }

    /**
     * Search for a transaction by hash or a block by hash or block number.
     *
     * Search returns one of Transaction or Block with an additional field `title`
     * (`"transaction"` or `"block"`) identifying which; the return type here is the
     * corresponding union. E.g. `timestamp_utc`: "2022-07-27 00:00:17".
     *
     * `api/search/<query>`
     * `curl https://xmrchain.net/api/search/12345 | jq`
     * @see Block::$title
     * @see Transaction::$title
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/aa96ce2927c050fabe17154a3bdfb09be83a632f/main.cpp#L734-L740
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/page.h#L5191-L5239
     *
     * @param string|int $value Transaction hash | block hash | block number.
     *
     * @throws UnexpectedValueException When the search result is neither a block nor a transaction.
     */
    public function getSearch($value): Block|Transaction
    {
        if (! (is_int($value) || is_string($value))) {
            throw new \InvalidArgumentException();
        }

        $endpoint = sprintf(
            'search/%s',
            (string) $value
        );

        $searchResult = $this->callApi($endpoint, \stdClass::class);

        $type = match ($searchResult->title ?? null) {
            'transaction' => Transaction::class,
            'block' => Block::class,
            default => throw new UnexpectedValueException(
                sprintf('Unexpected search result title `%s`.', $searchResult->title ?? '(none)')
            ),
        };

        return self::buildResponseMapper()->mapToClass($searchResult, $type);
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
        return $this->callApi('networkinfo', NetworkInfo::class);
    }

    /**
     * Total coins created: block rewards (coinbase) plus, separately, the sum
     * of all transaction fees. Monero has no supply cap; after the initial
     * emission curve, "tail emission" pays a constant 0.6 XMR per block, so
     * these values grow forever.
     *
     * NB: Emission is disabled by default (`xmrblocks --enable-emission-monitor`);
     * the monitor scans the whole chain on first run, so this endpoint may lag
     * a fresh instance.
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
        return $this->callApi('emission', Emission::class);
    }

    /**
     * Get transaction outputs
     *
     * Decode which of a transaction's outputs belong to an address.
     *
     * WHY THIS NEEDS A KEY: Monero outputs are paid to one-time (stealth)
     * addresses, so the chain never shows who received funds. A wallet's
     * secret VIEW KEY lets anyone holding it detect the wallet's INCOMING
     * outputs (never outgoing spends — those need the spend key).
     *
     * TWO DIRECTIONS OF PROOF:
     *  - `$txProve = false`: the RECIPIENT proves receipt — pass the
     *    recipient's address and secret view key.
     *  - `$txProve = true`: the SENDER proves they paid — pass the
     *    RECIPIENT's address and the transaction's TX KEY (per-transaction
     *    secret the sender's wallet holds) as `$viewkey`.
     *
     * PRIVACY WARNING: submitting an address + view key to a THIRD-PARTY
     * explorer permanently discloses that wallet's incoming transactions to
     * whoever operates the server. Prefer a self-hosted instance — this
     * repository ships one (see docker-compose.yml).
     *
     * `/api/outputs?txhash=%s&address=%s&viewkey=%s&txprove=%d`
     * `curl "http://127.0.0.1:8081/api/outputs?txhash=<txhash>&address=<address>&viewkey=<viewkey>&txprove=0" | jq`
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

        return $this->callApi($endpoint, Outputs::class);
    }

    /**
     * Search for our outputs in last few blocks (up to 5 blocks), using provided address and viewkey.
     *
     * Useful for detecting an expected incoming payment (optionally while it is
     * still unconfirmed, with `$mempool = true`) without running a wallet.
     *
     * PRIVACY WARNING: submitting an address + view key to a THIRD-PARTY
     * explorer permanently discloses that wallet's incoming transactions to
     * whoever operates the server. Prefer a self-hosted instance — this
     * repository ships one (see docker-compose.yml).
     *
     * `/api/outputsblocks?address=<address>&viewkey=<viewkey>&startblock=<n>&endblock=<n>&mempool=<0|1>`.
     * `api/outputsblocks?address=<address>&viewkey=<viewkey>&startblock=126&endblock=130&mempool=1`
     * NB: `$limit` is preserved as a convenience — the last `$limit` blocks are
     * resolved to a startblock/endblock range against the current tip.
     * @see OutputsBlocks
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/aa96ce2927c050fabe17154a3bdfb09be83a632f/main.cpp#L792-L827
     * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/page.h#L5492-L5672
     *
     * @param string $address
     * @param string $viewkey
     * @param int<1,5> $limit Max (up to 5) number of blocks to return.
     * @param bool   $mempool Additionally check in the mempool.
     */
    public function getOutputsBlocks(
        string $address,
        string $viewkey,
        int $limit = 5,
        bool $mempool = false
    ): OutputsBlocks {
//        trigger_error('Full `outputsblocks` JSON parsing not yet implemented', E_USER_WARNING);

        $sanitized_limit = max(1, min($limit, 5));

        if ($limit !== $sanitized_limit) {
            trigger_error(
                "ExplorerApi::getOutputsBlocks() Limit must be between 1 and 5, $limit used.",
                E_USER_WARNING
            );
        }

        if (empty($address)) {
            // @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/page.h#L5525-L5529
            throw new \InvalidArgumentException("Monero address not provided");
        }

        if (empty($viewkey)) {
            // @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/page.h#L5532-L5537
            throw new \InvalidArgumentException("Viewkey not provided");
        }

        // This explorer version takes an explicit [startblock, endblock] range
        // (older versions took `limit`). Preserve the "scan the last N blocks"
        // semantics by resolving the current tip and scanning back `$limit`
        // blocks. `height` is a block COUNT, so the tip block NUMBER is height-1.
        $tipBlock = $this->getNetworkInfo()->height - 1;
        $startBlock = max(0, $tipBlock - $sanitized_limit + 1);

        $endpoint = sprintf(
            'outputsblocks?address=%s&viewkey=%s&startblock=%d&endblock=%d&mempool=%d',
            $address,
            $viewkey,
            $startBlock,
            $tipBlock,
            (int) $mempool
        );

        return $this->callApi($endpoint, OutputsBlocks::class);
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
        return $this->callApi('version', Version::class);
    }

    /**
     * Builds the JsonMapper used to hydrate every API response.
     *
     * This is the ONE construction site for response mapping configuration
     * (snake_case JSON keys → camelCase PHP properties). Value-object factories
     * (amounts, dates, enums) will be registered here.
     *
     * Public so fixture-based tests hydrate through the exact pipeline
     * production uses.
     */
    public static function buildResponseMapper(): JsonMapperInterface
    {
        $factoryRegistry = (new FactoryRegistry())
            ->addFactory(stdClass::class, static fn ($value) => (object) $value);

        return (new JsonMapperBuilder())
            ->withPropertyMapper(new PropertyMapper($factoryRegistry))
            ->withDocBlockAnnotationsMiddleware()
            ->withTypedPropertiesMiddleware()
            ->withNamespaceResolverMiddleware()
            ->withObjectConstructorMiddleware($factoryRegistry)
            ->withCaseConversionMiddleware(TextNotation::UNDERSCORE(), TextNotation::CAMEL_CASE())
            ->build();
    }

    /**
     * Queries the API via PSR client, parses the JSend envelope and casts the `data` value to an object.
     *
     * API responses are JSON encoded JSend compliant messages: `{"status": "success", "data": {...}}`,
     * with `status` of `fail`/`error` (and a `message`) when the request could not be served.
     *
     * The body is decoded ONCE, with JSON_BIGINT_AS_STRING: Monero atomic-unit values are unsigned
     * 64-bit integers which can exceed PHP_INT_MAX (mainnet's cumulative emission already does), and
     * a plain `json_decode()` silently corrupts them to lossy floats.
     *
     * @see https://github.com/omniti-labs/jsend
     *
     * @template T of object
     * @param string $endpoint The REST route, excluding the domain.
     * @param class-string<T> $type The object type to cast/deserialize the response to.
     * @return T
     *
     * @throws ClientExceptionInterface PSR HTTP client exception.
     * @throws JsonException When the response body is not valid JSON.
     * @throws UnexpectedValueException If the HTTP server returns a format not compliant with JSend.
     * @throws IncompleteExplorerResponseException When the response cannot be hydrated to $type.
     * @throws Exception When the API reports fail/error status.
     */
    protected function callApi(string $endpoint, string $type)
    {
        if (! class_exists($type)) {
            throw new UnexpectedValueException("{$type} class does not exist");
        }

        $request = $this->requestFactory->createRequest(
            'GET',
            "{$this->url}/api/$endpoint"
        );

        $response = $this->client->sendRequest($request);

        if (2 !== intdiv($response->getStatusCode(), 100)) {
            // E.g. 404 when the JSON API is not enabled (`xmrblocks --enable-json-api`).
            throw new Exception(sprintf(
                'HTTP %d querying api/%s. Is the explorer running with `--enable-json-api`?',
                $response->getStatusCode(),
                strstr($endpoint, '?', true) ?: $endpoint
            ));
        }

        $responseBody = (string) $response->getBody();

        $decoded = json_decode($responseBody, false, 512, JSON_BIGINT_AS_STRING | JSON_THROW_ON_ERROR);

        if (! is_object($decoded) || ! isset($decoded->status)) {
            throw new UnexpectedValueException("api/{$endpoint} response is not a JSend envelope.");
        }

        if ($decoded->status !== 'success') {
            throw new Exception(sprintf(
                'API: %s%s',
                $decoded->status,
                isset($decoded->message) ? ": {$decoded->message}" : ''
            ));
        }

        self::assertRequiredResponseFieldsPresent($decoded->data ?? new stdClass(), $type, $endpoint, $responseBody);

        try {
            return self::buildResponseMapper()->mapToClassFromString(
                (string) json_encode($decoded->data ?? new stdClass()),
                $type
            );
        } catch (Throwable $throwable) {
            // The raw body is attached to the exception object, NOT the message:
            // outputs/outputsblocks responses echo the caller's view key.
            throw new IncompleteExplorerResponseException(
                sprintf(
                    'api/%s response could not be hydrated to %s (keys present: %s): %s',
                    strstr($endpoint, '?', true) ?: $endpoint,
                    $type,
                    implode(', ', array_keys(get_object_vars($decoded->data ?? new stdClass()))),
                    $throwable->getMessage()
                ),
                $responseBody,
                $throwable
            );
        }
    }

    /**
     * Throws when the response omits a field the model declares as required.
     *
     * The JsonMapper constructor middleware silently FABRICATES defaults for
     * missing constructor arguments (a missing `int` becomes `0`) — dangerous
     * for data consumers may make payment decisions on. Model constructor
     * parameters without a default value are therefore treated as REQUIRED and
     * validated here, before hydration; parameters that monerod/xmrblocks
     * legitimately omit must be declared nullable with a `= null` default (or
     * `= []` for lists) and the evidence noted in their docblock.
     *
     * NB: validates the top-level response object only; nested objects are
     * protected by the mapper's own null-into-non-nullable failure.
     *
     * @param object $data The decoded JSend `data` payload.
     * @param class-string $type The model the payload will be hydrated to.
     *
     * @throws IncompleteExplorerResponseException
     */
    protected static function assertRequiredResponseFieldsPresent(
        object $data,
        string $type,
        string $endpoint,
        string $responseBody
    ): void {
        $constructor = (new \ReflectionClass($type))->getConstructor();
        if (null === $constructor) {
            return;
        }

        $presentPropertyNames = array_map(
            // snake_case JSON keys are mapped to camelCase constructor parameters.
            static fn (string $key): string => lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key)))),
            array_keys(get_object_vars($data))
        );

        $missingRequiredPropertyNames = [];
        foreach ($constructor->getParameters() as $parameter) {
            if (! $parameter->isOptional() && ! in_array($parameter->getName(), $presentPropertyNames, true)) {
                $missingRequiredPropertyNames[] = $parameter->getName();
            }
        }

        if (empty($missingRequiredPropertyNames)) {
            return;
        }

        throw new IncompleteExplorerResponseException(
            sprintf(
                'api/%s response is missing required field(s) [%s] of %s (keys present: %s).',
                strstr($endpoint, '?', true) ?: $endpoint,
                implode(', ', $missingRequiredPropertyNames),
                $type,
                implode(', ', $presentPropertyNames)
            ),
            $responseBody
        );
    }
}
