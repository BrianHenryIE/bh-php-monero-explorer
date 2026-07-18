<?php

/**
 * @link https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/bfa342ed50090c1e227fd0b344b40fa02296a112/src/page.h#L4997-L5113
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class Transactions
{
    /**
     * @param TransactionsBlock[] $blocks
     */
    public function __construct(
        /**
         * @var TransactionsBlock[]
         */
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
        /**
         * @var int
         */
        public int $page,
        /**
         * @var int
         */
        public int $totalPageNo,
    ) {
    }
}
