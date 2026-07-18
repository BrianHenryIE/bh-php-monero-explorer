<?php

/**
 * @link https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/bfa342ed50090c1e227fd0b344b40fa02296a112/src/page.h#L4625-L4701
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class RawTransaction
{
    /**
     * @param RawTransactionVin[]  $vin
     * @param RawTransactionVout[] $vout
     */
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
        /**
         * @var int
         */
        public int $version,
        /**
         * @var RawTransactionVin[]
         */
        public array $vin,
        /**
         * @var RawTransactionVout[]
         */
        public array $vout,
    ) {
    }
}
