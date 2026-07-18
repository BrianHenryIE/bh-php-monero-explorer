<?php

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class RawTransactionVout
{
    public function __construct(
        /**
         * Amount in atomic units; `0` for RingCT outputs, whose amounts are hidden.
         *
         * @var int
         */
        public int $amount,
        /**
         * @var RawTransactionVoutTarget
         */
        public RawTransactionVoutTarget $target,
    ) {
    }
}
