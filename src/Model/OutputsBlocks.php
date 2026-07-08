<?php

/**
 * Class returned from /api/outputsblocks
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class OutputsBlocks
{
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
         * Number of recent blocks scanned, echoed back AS A STRING by upstream.
         *
         * @var string
         */
        public string $limit,
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
