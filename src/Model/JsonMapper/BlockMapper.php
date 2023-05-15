<?php

namespace BrianHenryIE\MoneroExplorer\Model\JsonMapper;

use BrianHenryIE\MoneroExplorer\Model\Block;

class BlockMapper implements Block
{
    protected int $blockHeight;
    protected int $currentHeight;
    protected string $hash;
    protected int $size;
    protected int $timestamp;
    protected string $timestampUtc;

    /** @var BlockTxMapper[] $txs */
    protected array $txs;

    /**
     * @inheritDoc
     */
    public function getBlockHeight(): int
    {
        return $this->blockHeight;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentHeight(): int
    {
        return $this->currentHeight;
    }

    /**
     * @inheritDoc
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @inheritDoc
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @inheritDoc
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @inheritDoc
     */
    public function getTimestampUtc(): string
    {
        return $this->timestampUtc;
    }

    /**
     *  @inheritDoc
     */
    public function getTxs(): array
    {
        return $this->txs;
    }

    /**
      *  @param int $blockHeight
      */
    public function setBlockHeight(int $blockHeight): void
    {
        $this->blockHeight = $blockHeight;
    }

    /**
      *  @param int $currentHeight
      */
    public function setCurrentHeight(int $currentHeight): void
    {
        $this->currentHeight = $currentHeight;
    }

    /**
      *  @param string $hash
      */
    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    /**
      *  @param int $size
      */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
      *  @param int $timestamp
      */
    public function setTimestamp(int $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
      *  @param string $timestampUtc
      */
    public function setTimestampUtc(string $timestampUtc): void
    {
        $this->timestampUtc = $timestampUtc;
    }

    /**
      *  @param BlockTxMapper[] $txs
      */
    public function setTxs(array $txs): void
    {
        $this->txs = $txs;
    }
}
