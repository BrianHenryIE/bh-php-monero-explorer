<?php
/**
 * Verifying payments received.
 *
 * In this example we make a sale to a customer when the Monero blockchain height is 2676045 and give them a payment
 * address. We then loop through the blocks that have been created since, and query each transaction in the blocks with
 * our secret view key until we confirm the payment has been received.
 *
 * We know:
 * * Payment address: 85wNBbdQcBM5cg49zk45Q7GetHVRWrUu53J4BuNEwaxPcWPrGiCAjvueC1ZR2YSn7MQveixbCtCd1LAA92ej3hvDGVRSks5
 * * Transaction id:  2773f57e0b8355e4eb1c8578a860ae8fa18ba04ce287f317b9ac015d42e3ee24
 * * Secret viewkey:  5248dc9138538c8de07706e6bb7b5b8fcd928306d1f3b9ff2c3027f70671de00
 * * Block:           2676047
 *
 * @see https://www.exploremonero.com/deposit
 */

$startingBlockHeight = 2676045;
$paymentAddress      = '85wNBbdQcBM5cg49zk45Q7GetHVRWrUu53J4BuNEwaxPcWPrGiCAjvueC1ZR2YSn7MQveixbCtCd1LAA92ej3hvDGVRSks5';
$secretViewkey       = '5248dc9138538c8de07706e6bb7b5b8fcd928306d1f3b9ff2c3027f70671de00';

require __DIR__ . '/../vendor/autoload.php';

/** @var Psr\Http\Message\RequestFactoryInterface $requestFactory */
$requestFactory = new \GuzzleHttp\Psr7\HttpFactory();

/** @var Psr\Http\Client\ClientInterface $client */
$client = new \GuzzleHttp\Client();

$explorerTools = new \BrianHenryIE\MoneroExplorer\ExplorerTools($requestFactory, $client);

$paid = false;

$blockchainHeight = $explorerTools->getLastBlockHeight();

echo "Beginning search.\n";
echo "Payment address:   $paymentAddress\n";
echo "Secret viewkey:    $secretViewkey\n";
echo "Blockchain height: $blockchainHeight\n";

foreach (range($startingBlockHeight, $blockchainHeight) as $blockNumber) {
    echo "Looking for payment in block $blockNumber\n";
    $paid = $explorerTools->isBlockContainsPayment($blockNumber, $paymentAddress, $secretViewkey);
    if ($paid) {
        break;
    }
}

echo $paid ? "Payment seen in block $blockNumber" : "Payment not found";

?>
```
Beginning search.
Payment address:   85wNBbdQcBM5cg49zk45Q7GetHVRWrUu53J4BuNEwaxPcWPrGiCAjvueC1ZR2YSn7MQveixbCtCd1LAA92ej3hvDGVRSks5
Secret viewkey:    5248dc9138538c8de07706e6bb7b5b8fcd928306d1f3b9ff2c3027f70671de00
Blockchain height: 2900136
Looking for payment in block 2676045
Looking for payment in block 2676046
Looking for payment in block 2676047
Payment seen in block 2676047
```