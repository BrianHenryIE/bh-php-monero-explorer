<?php

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class TransactionsBlock
{
    /**
     * @param BlockTx[] $txs
     */
    public function __construct(
        /**
         * Age of the block as `d:hh:mm:ss` (relative to the query time).
         *
         * @var string
         */
        public string $age,
        /** @var string */
        public string $hash,
        /** @var int */
        public int $height,
        /**
         * NB: upstream reports this in inconsistent magnitude (compare `Block::$size`).
         *
         * @var float
         */
        public float $size,
        /**
         * Epoch seconds the block was mined (set by the miner).
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
        /** @var BlockTx[] */
        public array $txs,
    ) {
    }
}
