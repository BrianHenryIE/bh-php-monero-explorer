<?php

/**
 * @link https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/bfa342ed50090c1e227fd0b344b40fa02296a112/src/page.h#L4901-L4989
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class RawBlock
{
    public function __construct(
        /**
         * @var int
         */
        public int $majorVersion,
        /**
         * @var RawBlockMinerTx
         */
        public RawBlockMinerTx $minerTx,
        /**
         * @var int
         */
        public int $minorVersion,
        /**
         * @var int
         */
        public int $nonce,
        /**
         * Hash of the previous block.
         *
         * @var string
         */
        public string $prevId,
        /**
         * Epoch seconds the block was mined (set by the miner).
         *
         * @var int
         */
        public int $timestamp,
        /**
         * Hashes of the non-coinbase transactions in the block.
         *
         * @var string[]
         */
        public array $txHashes,
    ) {
    }
}
