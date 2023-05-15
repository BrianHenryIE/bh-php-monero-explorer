<?php

namespace BrianHenryIE\MoneroExplorer\Model\JsonMapper;

use BrianHenryIE\MoneroExplorer\Model\RawBlock;
use BrianHenryIE\MoneroExplorer\Model\RawBlockMinerTx;

class RawBlockMapper implements RawBlock
{
    protected int $majorVersion;

    /** @var RawBlockMinerTxMapper $minerTx  */
    protected RawBlockMinerTx $minerTx;

    protected int $minorVersion;
    protected int $nonce;
    protected string $prevId;
    protected int $timestamp;

    /** @var array<int,string> $txHashes */
    protected array $txHashes;


    /**
     * @inheritDoc
     */
    public function getMajorVersion(): int
    {
        return $this->majorVersion;
    }

    /**
     * @inheritDoc
     */
    public function getMinerTx(): RawBlockMinerTx
    {
        return $this->minerTx;
    }

    /**
     * @inheritDoc
     */
    public function getMinorVersion(): int
    {
        return $this->minorVersion;
    }

    /**
     * @inheritDoc
     */
    public function getNonce(): int
    {
        return $this->nonce;
    }

    /**
     * @inheritDoc
     */
    public function getPrevId(): string
    {
        return $this->prevId;
    }

    /**
     * @inheritDoc
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     *
     *  @inheritDoc
     * @return string[]
     */
    public function getTxHashes(): array
    {
        return $this->txHashes;
    }

    /**
      *  @param int $majorVersion
      */
    public function setMajorVersion(int $majorVersion): void
    {
        $this->majorVersion = $majorVersion;
    }

    /**
      *  @param RawBlockMinerTxMapper $minerTx
      */
    public function setMinerTx(RawBlockMinerTxMapper $minerTx): void
    {
        $this->minerTx = $minerTx;
    }

    /**
      *  @param int $minorVersion
      */
    public function setMinorVersion(int $minorVersion): void
    {
        $this->minorVersion = $minorVersion;
    }

    /**
      *  @param int $nonce
      */
    public function setNonce(int $nonce): void
    {
        $this->nonce = $nonce;
    }

    /**
      *  @param string $prevId
      */
    public function setPrevId(string $prevId): void
    {
        $this->prevId = $prevId;
    }

    /**
      *  @param int $timestamp
      */
    public function setTimestamp(int $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
      *  @param array<int,string> $txHashes
      */
    public function setTxHashes(array $txHashes): void
    {
        $this->txHashes = $txHashes;
    }
}
