<?php

/**
 * Property in class returned from /api/outputs
 *
 * @see Outputs
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class OutputsOutput
{
    public function __construct(
        /**
         * Amount in atomic units this output pays to the queried address;
         * decoded with the supplied view key.
         *
         * @var int
         */
        public int $amount,
        /**
         * True when this output belongs to the queried address (per the view
         * key), false for the transaction's other outputs.
         *
         * @var bool
         */
        public bool $match,
        /**
         * Index of the output within its transaction.
         *
         * @var int
         */
        public int $outputIdx,
        /**
         * The one-time (stealth) public key of the output.
         *
         * @var string
         */
        public string $outputPubkey,
    ) {
    }
}
