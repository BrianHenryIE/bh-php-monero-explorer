<?php

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class Mempool
{
    public function __construct(
        /**
         * Max transactions requested (the API defaults to 100000000, i.e. "all").
         *
         * @var int
         */
        public int $limit,
        /** @var int */
        public int $page,
        /** @var int */
        public int $totalPageNo,
        /** @var MempoolTxs[] */
        public array $txs,
        /**
         * Total number of transactions in the mempool.
         *
         * @var int
         */
        public int $txsNo,
    ) {
    }
}
