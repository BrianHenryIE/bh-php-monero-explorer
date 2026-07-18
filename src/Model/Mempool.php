<?php

/**
 * @link https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/bfa342ed50090c1e227fd0b344b40fa02296a112/src/page.h#L5121-L5216
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer\Model;

/**
 * The mempool ("memory pool") holds transactions that have been broadcast but
 * not yet mined into a block; they have zero confirmations and MAY still be
 * dropped or never confirm. Each node has its OWN mempool — contents differ
 * slightly between nodes depending on propagation — so this reflects the
 * explorer's backing node, not a global truth.
 */
final readonly class Mempool
{
    /**
     * @param MempoolTxs[] $txs
     */
    public function __construct(
        /**
         * Max transactions requested (the API defaults to 100000000, i.e. "all").
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
        /**
         * @var MempoolTxs[]
         */
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
