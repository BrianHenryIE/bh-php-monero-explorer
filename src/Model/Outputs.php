<?php

/**
 * Class returned from /api/outputs
 *
 * @link https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/bfa342ed50090c1e227fd0b344b40fa02296a112/src/page.h#L5289-L5516
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer\Model;

use BrianHenryIE\MoneroExplorer\ExplorerApi;

/**
 * @see ExplorerApi::getOutputs()
 */
final readonly class Outputs
{
    /**
     * @param OutputsOutput[] $outputs
     */
    public function __construct(
        /**
         * The queried address (as hex-decoded public spend/view key pair).
         *
         * @var string
         */
        public string $address,
        /**
         * @var OutputsOutput[]
         */
        public array $outputs,
        /**
         * @var int
         */
        public int $txConfirmations,
        /**
         * @var string
         */
        public string $txHash,
        /**
         * True when proving a SENT payment (the supplied key was a tx private
         * key rather than the recipient's view key).
         *
         * @var bool
         */
        public bool $txProve,
        /**
         * Epoch seconds of the transaction's block.
         *
         * @var int
         */
        public int $txTimestamp,
        /**
         * The view key (or tx private key when `txProve`) echoed back, as hex.
         *
         * @var string
         */
        public string $viewkey,
    ) {
    }
}
