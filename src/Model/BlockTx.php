<?php

/**
 * Model for the `txs` array in Block objects.
 */

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class BlockTx
{
    public function __construct(
        /**
         * True when this is the block's miner (reward) transaction.
         *
         * @var bool
         */
        public bool $coinbase,
        /**
         * The tx_extra field as hex (tx public key, encrypted payment id, …).
         *
         * @var string
         */
        public string $extra,
        /**
         * Number of decoys in each ring (ring size − 1).
         *
         * @var int
         */
        public int $mixin,
        /**
         * Legacy unencrypted payment id; empty for modern transactions.
         *
         * @var string
         */
        public string $paymentId,
        /**
         * Encrypted 8-byte payment id (from an integrated address); empty when none.
         *
         * @var string
         */
        public string $paymentId8,
        /**
         * RingCT type; `0` for pre-RingCT and coinbase transactions.
         *
         * @var int
         */
        public int $rctType,
        /**
         * Fee in atomic units; `0` for coinbase transactions.
         *
         * @var int
         */
        public int $txFee,
        /** @var string */
        public string $txHash,
        /**
         * Size in bytes.
         *
         * @var int
         */
        public int $txSize,
        /** @var int */
        public int $txVersion,
        /**
         * Sum of input amounts; `0` when amounts are hidden by RingCT.
         *
         * @var int
         */
        public int $xmrInputs,
        /**
         * Sum of output amounts; `0` when amounts are hidden by RingCT.
         *
         * @var int
         */
        public int $xmrOutputs,
    ) {
    }
}
