<?php

/**
 * Class returned from /api/outputsblocks
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class OutputsBlocks
{
    /**
     * @param OutputsBlocksOutput[] $outputs
     */
    public function __construct(
        /**
         * The queried address (as hex-decoded public spend/view key pair).
         *
         * @var string
         */
        public string $address,
        /**
         * The chain height at the time of the query.
         *
         * @var int
         */
        public int $height,
        /**
         * First block scanned (inclusive), echoed back from the request.
         *
         * @var int
         */
        public int $startblock,
        /**
         * Last block scanned (inclusive), echoed back from the request.
         *
         * @var int
         */
        public int $endblock,
        /**
         * Whether the mempool was also scanned.
         *
         * @var bool
         */
        public bool $mempool,
        /**
         * Outputs found for the address; empty when none in the scanned range.
         *
         * @var OutputsBlocksOutput[]
         */
        public array $outputs,
        /**
         * The view key echoed back, as hex.
         *
         * @var string
         */
        public string $viewkey,
    ) {
    }
}
