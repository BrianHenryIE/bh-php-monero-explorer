<?php

namespace BrianHenryIE\MoneroExplorer\Model;

interface NetworkInfo
{
    /**
      *
      */
    public function getAltBlocksCount(): int;

    /**
      *
      */
    public function getBlockSizeLimit(): int;

    /**
      *
      */
    public function getBlockSizeMedian(): int;

    /**
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
      *
      */
    public function getGreyPeerlistSize(): int;

    /**
      *
      */
    public function getHashRate(): int;

    /**
      *
      */
    public function getHeight(): int;

    /**
      *
      */
    public function getIncomingConnectionsCount(): int;

    /**
      *
      */
    public function getOutgoingConnectionsCount(): int;

    /**
      *
      */
    public function isStagenet(): bool;

    /**
      *
      */
    public function getStartTime(): int;

    /**
      *
      */
    public function isStatus(): bool;

    /**
      *
      */
    public function getTarget(): int;

    /**
      *
      */
    public function getTargetHeight(): int;

    /**
      *
      */
    public function isTestnet(): bool;

    /**
      *
      */
    public function getTopBlockHash(): string;

    /**
      *
      */
    public function getTxCount(): int;

    /**
      *
      */
    public function getTxPoolSize(): int;

    /**
      *
      */
    public function getTxPoolSizeKbytes(): int;

    /**
      *
      */
    public function getWhitePeerlistSize(): int;
}
