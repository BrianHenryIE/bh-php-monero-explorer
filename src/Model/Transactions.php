<?php

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class Transactions
{
    public function __construct(
        /** @var TransactionsBlock[] */
        public array $blocks,
        /**
         * The chain height at the time of the query.
         *
         * @var int
         */
        public int $currentHeight,
        /**
         * Transactions per page, as requested.
         *
         * @var int
         */
        public int $limit,
        /** @var int */
        public int $page,
        /** @var int */
        public int $totalPageNo,
    ) {
    }
}
