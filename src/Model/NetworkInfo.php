<?php

/**
 * @see https://www.getmonero.org/resources/developer-guides/daemon-rpc.html#get_info
 */

namespace BrianHenryIE\MoneroExplorer\Model;

interface NetworkInfo
{
    /**
      *
      */
    public function getAltBlocksCount(): int;

    /**
     * Maximum allowed adjusted block size based on latest 100000 blocks
     *
      * Aka: block_weight_limit.
      */
    public function getBlockSizeLimit(): int;

    /**
     * Median adjusted block size of latest 100000 blocks.
     *
      * Aka: block_weight_median.
      */
    public function getBlockSizeMedian(): int;

    /**
     * Least-significant 64 bits of the 128-bit cumulative difficulty.
     *
     */
    public function getCumulativeDifficulty(): string;

    /**
      *
      */
    public function isCurrent(): bool;

    /**
      *
      */
    public function getCurrentHfVersion(): int;

    /**
      *
      */
    public function getDifficulty(): string;

    /**
      *
      */
    public function getFeeEstimate(): int;

    /**
      *
      */
    public function getFeePerKb(): int;

    /**
      * Grey Peerlist Size
      */
    public function getGreyPeerlistSize(): int;

    /**
      *
      */
    public function getHashRate(): int;

    /**
      * Current length of longest chain known to daemon.
      */
    public function getHeight(): int;

    /**
      * Number of peers connected to and pulling from "your" node.
      */
    public function getIncomingConnectionsCount(): int;

    /**
      * Number of peers that "you" are connected to and getting information from.
      */
    public function getOutgoingConnectionsCount(): int;

    /**
      * States if the node is on the stagenet (true) or not (false).
      */
    public function isStagenet(): bool;

    /**
      * Start time of the daemon, as UNIX time.
      */
    public function getStartTime(): int;

    /**
      * General RPC error code. "OK" means everything looks good.
     * 0|CORE_RPC_STATUS_OK|CORE_RPC_STATUS_BUSY
     *
     *
     * TODO: check type. It looks like a bool in the returned JSON, but looks like an ENUM in the C code, and is defined
     * as a string in `developer-guides/daemon-rpc.html`.
      */
    public function isStatus(): bool;

    /**
      * Current target for next proof of work.
      */
    public function getTarget(): int;

    /**
      * The height of the next block in the chain.
      */
    public function getTargetHeight(): int;

    /**
      * States if the node is on the testnet (true) or not (false).
      */
    public function isTestnet(): bool;

    /**
      * Hash of the highest block in the chain.
      */
    public function getTopBlockHash(): string;

    /**
      * Total number of non-coinbase transaction in the chain.
      */
    public function getTxCount(): int;

    /**
      * Number of transactions that have been broadcast but not included in a block.
      */
    public function getTxPoolSize(): int;

    /**
      *
      */
    public function getTxPoolSizeKbytes(): int;

    /**
     * White Peerlist Size.
     *
     * TODO:
     */
    public function getWhitePeerlistSize(): int;
}
