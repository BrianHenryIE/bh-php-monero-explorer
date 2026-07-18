<?php

/**
 * @link https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/bfa342ed50090c1e227fd0b344b40fa02296a112/src/page.h#L4429-L4616
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class Transaction
{
    /**
     * @param TransactionInput[]  $inputs
     * @param TransactionOutput[] $outputs
     */
    public function __construct(
        /**
         * Height of the block containing the transaction.
         *
         * @var int
         */
        public int $blockHeight,
        /**
         * True when this is a miner (reward) transaction.
         *
         * @var bool
         */
        public bool $coinbase,
        /**
         * Blocks mined on top of (and including) this transaction's block.
         * Each confirmation makes reversal exponentially less likely; Monero
         * convention treats 10 as final, and coinbase outputs unlock after 60.
         *
         * @var int
         */
        public int $confirmations,
        /**
         * The chain height at the time of the query.
         *
         * @var int
         */
        public int $currentHeight,
        /**
         * The tx_extra field as hex (tx public key, encrypted payment id, …).
         *
         * @var string
         */
        public string $extra,
        /**
         * @var TransactionInput[]
         */
        public array $inputs,
        /**
         * Number of decoys in each ring (ring size − 1).
         *
         * @var int
         */
        public int $mixin,
        /**
         * @var TransactionOutput[]
         */
        public array $outputs,
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
         * @var int
         */
        public int $rctType,
        /**
         * Epoch seconds of the containing block.
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
         * Fee in atomic units; `0` for coinbase transactions.
         *
         * @var int
         */
        public int $txFee,
        /**
         * @var string
         */
        public string $txHash,
        /**
         * Size in bytes.
         *
         * @var int
         */
        public int $txSize,
        /**
         * @var int
         */
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
        /**
         * `"transaction"` when this object came from the `search` endpoint; absent
         * from the `transaction` endpoint's response.
         *
         * @var ?string
         */
        public ?string $title = null,
    ) {
    }
}
