<?php

/**
 * ExplorerTools defines some helper functions around ExplorerApi's raw requests.
 *
 * @author Serhack
 * @author cryptochangements
 * @author mosu-forge
 * @author BrianHenryIE
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

declare(strict_types=1);

namespace BrianHenryIE\MoneroExplorer;

use BrianHenryIE\MoneroExplorer\Model\OutputsOutput;
use Exception;

/**
 * TODO: It would be ideal if this implemented a common interface that Monero-RPC implemented.
 */
class ExplorerTools extends ExplorerApi
{
    public const MAINNET_URL = 'https://xmrchain.net';
    public const TESTNET_URL = 'https://testnet.xmrchain.com';

    /**
     * Query for the number of the last completed block of the Monero blockchain.
     *
     * @see https://www.exploremonero.com/info
     */
    public function getLastBlockHeight(): int
    {
        return $this->getNetworkInfo()->getHeight() - 1;
    }

    /**
     * Fetches a block by height and loops over its transactions
     *
     * @param int $blockHeight The block to query.
     * @param string $paymentAddress The receiving address.
     * @param string $viewkey The viewkey – secret viewkey when receiving payments, the transaction viewkey when proving payments.
     * @param bool $txProve
     *
     * @return bool
     */
    public function isBlockContainsPayment(int $blockHeight, string $paymentAddress, string $viewkey, bool $txProve = false): bool
    {
        $block = $this->getBlock($blockHeight);

        foreach ($block->getTxs() as $transaction) {
            $outputs = $this->getOutputs(
                $transaction->getTxHash(),
                $paymentAddress,
                $viewkey,
                false
            );

            foreach ($outputs->getOutputs() as $output) {
                if ($output->isMatch()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     *
     *
     * @param string $payment_id
     * @param string $payment_address
     * @param string $viewkey
     *
     * @return array<array{amount:string,tx_id:string,height:int}>
     */
    public function verifyPaymentInMempool(string $payment_id, string $payment_address, string $viewkey): array
    {
	    throw new Exception('Not yet implemented');

	    $txs     = array();
        $outputs = $this->getOutputsBlocks($payment_address, $viewkey)->getOutputs();
        foreach ($outputs as $payment) {
            // TODO OutputsBlocksOutput not yet implemented.
            if ($payment_id === $payment->getPaymentId()) {
                $txs[] = array(
                    'amount' => $payment->getAmount(),
                    'tx_id'  => $payment->getTxHash(),
                    'height' => $payment->getBlockNo(),
                );
            }
        }
        return $txs;
    }
}
