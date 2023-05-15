<?php

namespace BrianHenryIE\MoneroExplorer\Model\JsonMapper;

use BrianHenryIE\MoneroExplorer\Model\NetworkInfo;

class NetworkInfoMapper implements NetworkInfo
{
    protected int $altBlocksCount;
    protected int $blockSizeLimit;
    protected int $blockSizeMedian;
    protected string $cumulativeDifficulty;
    protected bool $current;
    protected int $currentHfVersion;
    protected string $difficulty;
    protected int $feeEstimate;
    protected int $feePerKb;
    protected int $greyPeerlistSize;
    protected int $hashRate;
    protected int $height;
    protected int $incomingConnectionsCount;
    protected int $outgoingConnectionsCount;
    protected bool $stagenet;
    protected int $startTime;
    protected bool $status;
    protected int $target;
    protected int $targetHeight;
    protected bool $testnet;
    protected string $topBlockHash;
    protected int $txCount;
    protected int $txPoolSize;
    protected int $txPoolSizeKbytes;
    protected int $whitePeerlistSize;


    /**
     * @inheritDoc
     */
    public function getAltBlocksCount(): int
    {
        return $this->altBlocksCount;
    }

    /**
     * @inheritDoc
     */
    public function getBlockSizeLimit(): int
    {
        return $this->blockSizeLimit;
    }

    /**
     * @inheritDoc
     */
    public function getBlockSizeMedian(): int
    {
        return $this->blockSizeMedian;
    }

    /**
     * @inheritDoc
     */
    public function getCumulativeDifficulty(): string
    {
        return $this->cumulativeDifficulty;
    }

    /**
     * @inheritDoc
     */
    public function isCurrent(): bool
    {
        return $this->current;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentHfVersion(): int
    {
        return $this->currentHfVersion;
    }

    /**
     * @inheritDoc
     */
    public function getDifficulty(): string
    {
        return $this->difficulty;
    }

    /**
     * @inheritDoc
     */
    public function getFeeEstimate(): int
    {
        return $this->feeEstimate;
    }

    /**
     * @inheritDoc
     */
    public function getFeePerKb(): int
    {
        return $this->feePerKb;
    }

    /**
     * @inheritDoc
     */
    public function getGreyPeerlistSize(): int
    {
        return $this->greyPeerlistSize;
    }

    /**
     *
     */
    public function getHashRate(): int
    {
        return $this->hashRate;
    }

    /**
     *
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     *
     */
    public function getIncomingConnectionsCount(): int
    {
        return $this->incomingConnectionsCount;
    }

    /**
     *
     */
    public function getOutgoingConnectionsCount(): int
    {
        return $this->outgoingConnectionsCount;
    }

    /**
     * @inheritDoc
     */
    public function isStagenet(): bool
    {
        return $this->stagenet;
    }

    /**
     *
     */
    public function getStartTime(): int
    {
        return $this->startTime;
    }

    /**
     * @inheritDoc
     */
    public function isStatus(): bool
    {
        return $this->status;
    }

    /**
     *
     */
    public function getTarget(): int
    {
        return $this->target;
    }

    /**
     *
     */
    public function getTargetHeight(): int
    {
        return $this->targetHeight;
    }

    /**
     * @inheritDoc
     */
    public function isTestnet(): bool
    {
        return $this->testnet;
    }

    /**
     *
     */
    public function getTopBlockHash(): string
    {
        return $this->topBlockHash;
    }

    /**
     *
     */
    public function getTxCount(): int
    {
        return $this->txCount;
    }

    /**
     *
     */
    public function getTxPoolSize(): int
    {
        return $this->txPoolSize;
    }

    /**
     *
     */
    public function getTxPoolSizeKbytes(): int
    {
        return $this->txPoolSizeKbytes;
    }

    /**
     *
     */
    public function getWhitePeerlistSize(): int
    {
        return $this->whitePeerlistSize;
    }

    /**
      *  @param int $altBlocksCount
      */
    public function setAltBlocksCount(int $altBlocksCount): void
    {
        $this->altBlocksCount = $altBlocksCount;
    }

    /**
      *  @param int $blockSizeLimit
      */
    public function setBlockSizeLimit(int $blockSizeLimit): void
    {
        $this->blockSizeLimit = $blockSizeLimit;
    }

    /**
      *  @param int $blockSizeMedian
      */
    public function setBlockSizeMedian(int $blockSizeMedian): void
    {
        $this->blockSizeMedian = $blockSizeMedian;
    }

    /**
      *  @param string $cumulativeDifficulty
      */
    public function setCumulativeDifficulty(string $cumulativeDifficulty): void
    {
        $this->cumulativeDifficulty = $cumulativeDifficulty;
    }

    /**
      *  @param bool $current
      */
    public function setCurrent(bool $current): void
    {
        $this->current = $current;
    }

    /**
      *  @param int $currentHfVersion
      */
    public function setCurrentHfVersion(int $currentHfVersion): void
    {
        $this->currentHfVersion = $currentHfVersion;
    }

    /**
      *  @param string $difficulty
      */
    public function setDifficulty(string $difficulty): void
    {
        $this->difficulty = $difficulty;
    }

    /**
      *  @param int $feeEstimate
      */
    public function setFeeEstimate(int $feeEstimate): void
    {
        $this->feeEstimate = $feeEstimate;
    }

    /**
      *  @param int $feePerKb
      */
    public function setFeePerKb(int $feePerKb): void
    {
        $this->feePerKb = $feePerKb;
    }

    /**
      *  @param int $greyPeerlistSize
      */
    public function setGreyPeerlistSize(int $greyPeerlistSize): void
    {
        $this->greyPeerlistSize = $greyPeerlistSize;
    }

    /**
      *  @param int $hashRate
      */
    public function setHashRate(int $hashRate): void
    {
        $this->hashRate = $hashRate;
    }

    /**
      *  @param int $height
      */
    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    /**
      *  @param int $incomingConnectionsCount
      */
    public function setIncomingConnectionsCount(int $incomingConnectionsCount): void
    {
        $this->incomingConnectionsCount = $incomingConnectionsCount;
    }

    /**
      *  @param int $outgoingConnectionsCount
      */
    public function setOutgoingConnectionsCount(int $outgoingConnectionsCount): void
    {
        $this->outgoingConnectionsCount = $outgoingConnectionsCount;
    }

    /**
      *  @param bool $stagenet
      */
    public function setStagenet(bool $stagenet): void
    {
        $this->stagenet = $stagenet;
    }

    /**
      *  @param int $startTime
      */
    public function setStartTime(int $startTime): void
    {
        $this->startTime = $startTime;
    }

    /**
      *  @param bool $status
      */
    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    /**
      *  @param int $target
      */
    public function setTarget(int $target): void
    {
        $this->target = $target;
    }

    /**
      *  @param int $targetHeight
      */
    public function setTargetHeight(int $targetHeight): void
    {
        $this->targetHeight = $targetHeight;
    }

    /**
      *  @param bool $testnet
      */
    public function setTestnet(bool $testnet): void
    {
        $this->testnet = $testnet;
    }

    /**
      *  @param string $topBlockHash
      */
    public function setTopBlockHash(string $topBlockHash): void
    {
        $this->topBlockHash = $topBlockHash;
    }

    /**
      *  @param int $txCount
      */
    public function setTxCount(int $txCount): void
    {
        $this->txCount = $txCount;
    }

    /**
      *  @param int $txPoolSize
      */
    public function setTxPoolSize(int $txPoolSize): void
    {
        $this->txPoolSize = $txPoolSize;
    }

    /**
      *  @param int $txPoolSizeKbytes
      */
    public function setTxPoolSizeKbytes(int $txPoolSizeKbytes): void
    {
        $this->txPoolSizeKbytes = $txPoolSizeKbytes;
    }

    /**
      *  @param int $whitePeerlistSize
      */
    public function setWhitePeerlistSize(int $whitePeerlistSize): void
    {
        $this->whitePeerlistSize = $whitePeerlistSize;
    }
}
