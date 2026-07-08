<?php

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class Emission
{
    public function __construct(
        /**
         * The block height the emission has been calculated up to.
         *
         * @var int
         */
        public int $blkNo,
        /**
         * Total coinbase emission in atomic units, as a numeric string:
         * mainnet's value (~1.8e19) exceeds PHP_INT_MAX, so it must not be
         * handled as int (or, worse, float).
         *
         * @var string
         */
        public string $coinbase,
        /**
         * Total transaction fees in atomic units, as a numeric string.
         *
         * @var string
         */
        public string $fee,
    ) {
    }
}
