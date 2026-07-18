<?php

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class RawTransactionVinKey
{
    public function __construct(
        /**
         * Amount in atomic units; `0` for RingCT inputs, whose amounts are hidden.
         *
         * @var int
         */
        public int $amount,
        /**
         * The input's key image.
         *
         * @var string
         */
        public string $kImage,
        /**
         * Relative offsets into the global output set identifying the ring members.
         *
         * @var int[]
         */
        public array $keyOffsets,
    ) {
    }
}
