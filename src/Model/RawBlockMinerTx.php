<?php

/**
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer\Model;

use stdClass;

final readonly class RawBlockMinerTx
{
    public function __construct(
        /**
         * The tx_extra field bytes.
         *
         * @var int[]
         */
        public array $extra,
        /**
         * Coinbase transactions carry only `{"type": 0}` here (no RingCT).
         *
         * @var stdClass
         */
        public stdClass $rctSignatures,
        /**
         * Coinbase outputs unlock 60 blocks after their block height.
         * NB: a block HEIGHT when < 500000000, an epoch TIMESTAMP otherwise.
         *
         * @var int
         */
        public int $unlockTime,
        /**
         * @var int
         */
        public int $version,
        /**
         * A coinbase input is `{"gen": {"height": n}}`, unlike a spend input.
         *
         * @var stdClass[]
         */
        public array $vin,
        /**
         * @var stdClass[]
         */
        public array $vout,
    ) {
    }
}
