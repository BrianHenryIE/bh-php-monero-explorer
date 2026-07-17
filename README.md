[![PHP 8.4](https://img.shields.io/badge/PHP-8.4-8892BF.svg)]() [![PHPCS PSR-12](https://img.shields.io/badge/PHPCS-PSR–12-226146.svg)](https://www.php-fig.org/psr/psr-12/) [![PHPUnit ](.github/coverage.svg)](https://brianhenryie.github.io/bh-php-monero-explorer/) [![PHPStan ](.github/phpstan.svg)](https://phpstan.org/)

# Monero Explorer PHP Client


A thin, strongly typed PHP SDK for [Onion Monero Blockchain Explorer](https://github.com/moneroexamples/onion-monero-blockchain-explorer/) instances' HTTP API, e.g. [xmrchain.net](https://xmrchain.net/).  

> [Monero](https://www.getmonero.org/) (XMR) is a private, decentralized cryptocurrency, developed with the goals of privacy and security first, ease of use and efficiency second. 

Fetching and parsing JSON is very easy with PHP, so this library's value comes from the typed classes and the documentation in PhpDoc. Please contribute clarifications to functions' purposes.

## Use

Before v1.0, function signatures are expected to change as they are properly documented.

```bash
composer require --fixed brianhenryie/bh-php-monero-explorer@dev
```

You also need a [PSR-7 implementation](https://packagist.org/providers/psr/http-client-implementation) and a [PSR-17 implementation](https://packagist.org/providers/psr/http-factory-implementation), the most popular being `guzzlehttp/guzzle`. 

`ExplorerApi` is a direct mapping of [API endpoints](https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/aa96ce2927c050fabe17154a3bdfb09be83a632f/main.cpp#L656-L837) to PHP functions.

```php
/** @var Psr\Http\Message\RequestFactoryInterface $requestFactory */
$requestFactory = new \GuzzleHttp\Psr7\HttpFactory();
/** @var Psr\Http\Client\ClientInterface $client */
$client = new \GuzzleHttp\Client();

$explorerApi = new \BrianHenryIE\MoneroExplorer\ExplorerApi( $requestFactory, $client );

/** @var \BrianHenryIE\MoneroExplorer\Model\NetworkInfo $networkInfo */
$networkInfo = $explorerApi->getNetworkInfo();

$lastBlockHeight = $networkInfo->height - 1;
```

Models are `final readonly` classes with public properties (PHP >= 8.4 is required).

> **Privacy:** `getOutputs()`/`getOutputsBlocks()` send an address and view key to the
> explorer server, permanently disclosing that wallet's incoming transactions to its
> operator. Prefer a self-hosted instance — this repository ships one:

```bash
make integration-up   # monerod (regtest) + monero-wallet-rpc + xmrblocks; first run compiles xmrblocks (~20 min)
make integration-seed # deterministic test chain: 131 blocks, two seeded payments
composer test-integration
make integration-down
```

`ExplorerTools` extends `ExplorerApi` to add convenience functions.

```php
$explorerTools = new \BrianHenryIE\MoneroExplorer\ExplorerTools( $requestFactory, $client );

$lastBlockHeight = $explorerTools->getLastBlockHeight();
```

### Accept a Monero payment

To accept a payment with Monero...

1. Share payment address with customer
1. Note the blockchain height at that time
1. Periodically/progressively check new blocks since then inspecting for payment

See: [`examples/VerifyingPaymentsReceived.php`](https://github.com/BrianHenryIE/bh-php-monero-explorer/blob/master/examples/VerifyingPaymentsReceived.php).

## Implementation

Initial `class-monero-explorer-tools.php` extracted from [monero-integrations/monerowp](https://github.com/monero-integrations/monerowp/blob/9ba2b640f7bd31441f9994dd66916bf480ed9016/include/class-monero-explorer-tools.php).

### Goals:

* [ ] Strongly typed: nested objects now map to models; a few responses (raw miner tx, detailed transaction) remain `stdClass`/arrays
* [ ] Unit tested: 100% should be achievable on what is just a thin wrapper
* [x] Use [PSR-7 HTTP client](https://www.php-fig.org/psr/psr-7/) & [PSR-17 HTTP factory](https://www.php-fig.org/psr/psr-17/)
* [ ] PhpDoc
* [x] Short tutorial

### Notes

* API is read-only
* API responses are JSON formatted using the [JSend](https://github.com/omniti-labs/jsend) convention

### Composer

Runtime dependencies are minimal: [JsonMapper/JsonMapper](https://github.com/JsonMapper/JsonMapper) ([JsonMapper.net](https://jsonmapper.net)) hydrates responses into the typed models, plus a consumer-supplied PSR-7/PSR-17 HTTP implementation. The [JSend](https://github.com/omniti-labs/jsend) response envelope is unwrapped directly in `ExplorerApi::callApi()` — no JSend library is required.


## Acknowledgements

* [Monero Onion Blockchain Explorer](https://github.com/moneroexamples/onion-monero-blockchain-explorer/graphs/contributors)
* [SerHack](https://github.com/serhack)
