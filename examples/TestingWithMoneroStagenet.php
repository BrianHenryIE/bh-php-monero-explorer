# Testing with Monero Stagenet

You'll likely want to run integration tests on your application. You can do this by creating wallets on Monero Stagenet and making payments between them.

### Create a wallet

We'll need Monero CLI tools `monero-wallet-cli` installed to begin (`brew install monero`).

TODO: Try trim the newline at the end of the secret viewkey

```bash
echo N | monero-wallet-cli --stagenet --generate-new-wallet tests/_data/wallets/monero-stagenet-wallet-1 --password password --mnemonic-language English --offline --log-file /dev/null | grep "View key" | cut -c 11- > tests/_data/wallets/monero-stagenet-wallet-1.secretviewkey.txt
```


| command                                                            | explain                                                                                                                                                              | 
|--------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| echo N \|                                                          | Reply No when prompted to start background mining                                                                                                                    |
| monero-wallet-cli                                                  | The Monero CLI tools application. run `montero-wallet-cli --help` for more.                                                                                          |
| --stagenet                                                         | Indicate we want to use Monero Stagenet                                                                                                                              |  
| --generate-new-wallet tests/_data/wallets/monero-stagenet-wallet-1 | Where to output the files: `monero-stagenet-wallet-1`, `monero-stagenet-wallet-1.address.txt`, and `monero-stagenet-wallet-1.keys`. The destination folder must exist. |
| --password password                                                |                                                                                                                                                                      | 
| --mnemonic-language English                                        |                                                                                                                                                                      | 
| --offline                                                          | Do not connect to a daemon                                                                                                                                           | 
| --log-file /dev/null                                               | Do not save logs to file                                                                                                                                             |
| \| grep "View key"                                                 | Capture the line containing "View key"                                                                                                                               |
| \| cut -c 11-                                                      | Cut off the first 11 characters                                                                                                                                      |
| > tests/_data/wallets/monero-stagenet-wallet-1.secretviewkey.txt   | Save it to the file `monero-stagenet-wallet-1.secretviewkey.txt`                                                                                                     |

`cat tests/_data/wallets/monero-stagenet-wallet-1.address.txt`
`cat tests/_data/wallets/monero-stagenet-wallet-1.secretviewkey.txt`

`monero-wallet-cli --stagenet --wallet-file tests/_data/wallets/monero-stagenet-wallet-1 --offline --log-file /dev/null --command viewkey`


https://community.rino.io/faucet/stagenet/

<?php
require __DIR__ . '/../vendor/autoload.php';

$address = '57AaCDLE3GeBsgrK513QiUFePcBbSp5QYhn2Q2S1P9DsceUmeVkvBMJBQDFTfS9f21LfhXJSWNGN16mvXQmffGsA7KCVBxh';
$secretViewKey = '64630bfeffdbbcf73ebe58d05a259e1731388def9104f6cbbe69ff8f2c3cd500';


/** @var Psr\Http\Message\RequestFactoryInterface $requestFactory */
$requestFactory = new \GuzzleHttp\Psr7\HttpFactory();

/** @var Psr\Http\Client\ClientInterface $client */
$client = new \GuzzleHttp\Client();

$stagenetTools = new \BrianHenryIE\MoneroExplorer\ExplorerTools($requestFactory, $client, \BrianHenryIE\MoneroExplorer\ExplorerTools::STAGENET_URL);

// Watch the mempool for the incoming transaction

$outputs = $stagenetTools->getOutputsBlocks($address, $secretViewKey, 5, true);

print_r( $outputs );

//$stagenetTools->getLastBlockHeight()

//$stagenetTools->isBlockContainsPayment()
?>



https://www.monero.how/tutorial-how-to-send-and-receive-monero-command-line