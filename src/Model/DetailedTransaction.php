<?php

namespace BrianHenryIE\MoneroExplorer\Model;

use stdClass;

/**
 * Response of the `detailedtransaction` endpoint.
 *
 * NB: upstream serializes its internal mstch TEMPLATE CONTEXT for this
 * endpoint, so every scalar arrives wrapped in a single-element array (e.g.
 * `"tx_hash": ["6093260d…"]`) and some numbers are zero-padded strings. The
 * properties here reflect that wire format faithfully rather than pretending
 * it is clean; unwrap with `$detailedTransaction->txHash[0]` etc.
 *
 * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/page.h#L4675-L4722
 */
final readonly class DetailedTransaction
{
    public function __construct(
        /**
         * Additional tx public keys (used for multi-destination transactions).
         *
         * @var mixed[]
         */
        public array $addTxPubKeys,
        /** @var string[] Zero-padded block height, e.g. `["02676047"]`. */
        public array $blkHeight,
        /** @var mixed[] */
        public array $blkTimestamp,
        /** @var int[] Epoch seconds of the containing block. */
        public array $blkTimestampUint,
        /** @var int[] */
        public array $confirmations,
        /** @var mixed[] */
        public array $deltaTime,
        /** @var bool[] */
        public array $enableAsHex,
        /** @var mixed[] The tx_extra field. */
        public array $extra,
        /** @var bool[] */
        public array $hasInputs,
        /** @var bool[] */
        public array $hasPaymentId,
        /** @var bool[] */
        public array $hasPaymentId8,
        /**
         * Inputs with per-ring-member detail (ages, timescales); entries are
         * themselves mstch-context shaped.
         *
         * @var stdClass[]
         */
        public array $inputs,
        /** @var int[] */
        public array $inputsNo,
        /** @var mixed[] Sum of input amounts, as formatted string. */
        public array $inputsXmrSum,
        /** @var bool[] */
        public array $isRingct,
        /** @var mixed[] */
        public array $maxMixTime,
        /** @var mixed[] */
        public array $minMixTime,
        /**
         * Outputs with global-index detail; entries are mstch-context shaped.
         *
         * @var stdClass[]
         */
        public array $outputs,
        /** @var int[] */
        public array $outputsNo,
        /** @var mixed[] Sum of output amounts, as formatted string. */
        public array $outputsXmrSum,
        /** @var mixed[] Fee paid per kB, as formatted string. */
        public array $payedForKB,
        /** @var string[] Legacy unencrypted payment id; empty for modern transactions. */
        public array $paymentId,
        /** @var string[] Encrypted 8-byte payment id; empty when none. */
        public array $paymentId8,
        /** @var int[] */
        public array $rctType,
        /** @var bool[] */
        public array $stagenet,
        /** @var bool[] */
        public array $testnet,
        /** @var mixed[] */
        public array $timescalesScale,
        /** @var mixed[] */
        public array $txBlkHeight,
        /** @var mixed[] Fee in atomic units. */
        public array $txFee,
        /** @var mixed[] */
        public array $txFeeMicro,
        /** @var string[] */
        public array $txHash,
        /** @var string[] */
        public array $txPrefixHash,
        /** @var string[] The transaction's public key (from tx_extra). */
        public array $txPubKey,
        /** @var mixed[] Size in kB, as formatted string. */
        public array $txSize,
        /** @var int[] */
        public array $txVersion,
    ) {
    }
}
