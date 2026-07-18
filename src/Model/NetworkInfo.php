<?php

/**
 * @see https://www.getmonero.org/resources/developer-guides/daemon-rpc.html#get_info
 */

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class NetworkInfo
{
    public function __construct(
        /**
         * @var int
         */
        public int $altBlocksCount,
        /**
         * Maximum allowed adjusted block size based on latest 100000 blocks.
         *
         * Aka: block_weight_limit.
         *
         * @var int
         */
        public int $blockSizeLimit,
        /**
         * Median adjusted block size of latest 100000 blocks.
         *
         * Aka: block_weight_median.
         *
         * @var int
         */
        public int $blockSizeMedian,
        /**
         * Least-significant 64 bits of the 128-bit cumulative difficulty.
         *
         * @var string
         */
        public string $cumulativeDifficulty,
        /**
         * @var bool
         */
        public bool $current,
        /**
         * @var int
         */
        public int $currentHfVersion,
        /**
         * @var string
         */
        public string $difficulty,
        /**
         * @var int
         */
        public int $feePerKb,
        /**
         * Grey Peerlist Size.
         *
         * @var int
         */
        public int $greyPeerlistSize,
        /**
         * @var int
         */
        public int $hashRate,
        /**
         * Current length of longest chain known to daemon.
         *
         * @var int
         */
        public int $height,
        /**
         * Number of peers connected to and pulling from "your" node.
         *
         * @var int
         */
        public int $incomingConnectionsCount,
        /**
         * Number of peers that "you" are connected to and getting information from.
         *
         * @var int
         */
        public int $outgoingConnectionsCount,
        /**
         * States if the node is on the stagenet (true) or not (false).
         *
         * @var bool
         */
        public bool $stagenet,
        /**
         * Start time of the daemon, as UNIX time.
         *
         * @var int
         */
        public int $startTime,
        /**
         * General RPC error code. "OK" means everything looks good.
         * 0|CORE_RPC_STATUS_OK|CORE_RPC_STATUS_BUSY
         *
         * TODO: check type. It looks like a bool in the returned JSON, but looks like an ENUM in the C code, and is
         * defined as a string in `developer-guides/daemon-rpc.html`.
         *
         * @var bool
         */
        public bool $status,
        /**
         * Current target for next proof of work.
         *
         * @var int
         */
        public int $target,
        /**
         * The height of the next block in the chain.
         *
         * @var int
         */
        public int $targetHeight,
        /**
         * States if the node is on the testnet (true) or not (false).
         *
         * @var bool
         */
        public bool $testnet,
        /**
         * Hash of the highest block in the chain.
         *
         * @var string
         */
        public string $topBlockHash,
        /**
         * Total number of non-coinbase transaction in the chain.
         *
         * @var int
         */
        public int $txCount,
        /**
         * Number of transactions that have been broadcast but not included in a block.
         *
         * @var int
         */
        public int $txPoolSize,
        /**
         * @var int
         */
        public int $txPoolSizeKbytes,
        /**
         * White Peerlist Size.
         *
         * TODO:
         *
         * @var int
         */
        public int $whitePeerlistSize,
        /**
         * Estimated fee in atomic units per kB.
         *
         * Optional: absent from older explorer versions' responses (e.g. the
         * captured tests/_data/explorer-tools/getLastBlockHeight.json fixture);
         * null when not reported.
         *
         * @var ?int
         */
        public ?int $feeEstimate = null,
    ) {
    }
}
