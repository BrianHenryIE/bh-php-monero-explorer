<?php

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class MempoolTxs
{
    public function __construct(
        /**
         * Always false: coinbase transactions are created in blocks, never pooled.
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
        /** @var int */
        public int $rctType,
        /**
         * Epoch seconds the transaction was received by this node's mempool.
         *
         * @var int
         */
        public int $timestamp,
        /**
         * E.g. `2022-07-27 00:00:17`.
         *
         * @var string
         */
        public string $timestampUtc,
        /**
         * Fee in atomic units.
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
