<?php

/**
 * One output belonging to the queried address, found by scanning recent
 * blocks (and optionally the mempool).
 *
 * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/page.h#L5953-L5959
 *
 * Which is partly common with:
 * @see \BrianHenryIE\MoneroExplorer\Model\OutputsOutput
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class OutputsBlocksOutput
{
    public function __construct(
        /**
         * Amount in atomic units, decoded with the supplied view key.
         *
         * @var int
         */
        public int $amount,
        /**
         * Height of the containing block; `0` when the tx is still in the mempool.
         *
         * @var int
         */
        public int $blockNo,
        /**
         * @var bool
         */
        public bool $inMempool,
        /**
         * Index of the output within its transaction.
         *
         * @var int
         */
        public int $outputIdx,
        /**
         * The one-time (stealth) public key of the output.
         *
         * @var string
         */
        public string $outputPubkey,
        /**
         * Decrypted payment id (from an integrated address), or legacy payment
         * id; empty when none.
         *
         * @var string
         */
        public string $paymentId,
        /**
         * @var string
         */
        public string $txHash,
    ) {
    }
}
