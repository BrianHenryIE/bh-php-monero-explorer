<?php

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class Block
{
    /**
     * @param BlockTx[] $txs
     */
    public function __construct(
        /**
         * @var int
         */
        public int $blockHeight,
        /**
         * The chain height at the time of the query.
         *
         * @var int
         */
        public int $currentHeight,
        /**
         * @var string
         */
        public string $hash,
        /**
         * Size in bytes.
         *
         * @var int
         */
        public int $size,
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
        /**
         * @var BlockTx[]
         */
        public array $txs,
        /**
         * `"block"` when this object came from the `search` endpoint; absent from
         * the `block` endpoint's response.
         *
         * @var ?string
         */
        public ?string $title = null,
    ) {
    }
}
