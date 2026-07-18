<?php

/**
 * @link https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/bfa342ed50090c1e227fd0b344b40fa02296a112/src/page.h#L5836-L5872
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

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
