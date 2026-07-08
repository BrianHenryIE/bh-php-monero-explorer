<?php

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class RawTransaction
{
    public function __construct(
        /**
         * The tx_extra field bytes (tx public key, encrypted payment id, …).
         *
         * @var int[]
         */
        public array $extra,
        /**
         * RingCT signature data.
         *
         * @var array<string, mixed>
         */
        public array $rctSignatures,
        /**
         * Prunable part of the RingCT signature data (range proofs etc.).
         *
         * @var array<string, mixed>
         */
        public array $rctsigPrunable,
        /**
         * NB: a block HEIGHT when < 500000000, an epoch TIMESTAMP otherwise.
         *
         * @var int
         */
        public int $unlockTime,
        /** @var int */
        public int $version,
        /** @var RawTransactionVin[] */
        public array $vin,
        /** @var RawTransactionVout[] */
        public array $vout,
    ) {
    }
}
