<?php

namespace BrianHenryIE\MoneroExplorer\Model\JsonMapper;

use BrianHenryIE\MoneroExplorer\Model\TransactionsBlock;

class TransactionsBlockMapper implements TransactionsBlock
{
    /**
     * "00:04:11"
     */
    protected string $age;
    protected string $hash;
    protected int $height;
    protected float $size;
    protected int $timestamp;
    protected string $timestamp_utc;

    /**
     * @var BlockTxMapper[]
     */
    protected array $txs;

    /**
     * @return string
     */
    public function getAge(): string
    {
        return $this->age;
    }

    /**
     * @param string $age
     */
    public function setAge(string $age): void
    {
        $this->age = $age;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    /**
     * @return float
     */
    public function getSize(): float
    {
        return $this->size;
    }

    /**
     * @param float $size
     */
    public function setSize(float $size): void
    {
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     */
    public function setTimestamp(int $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return string
     */
    public function getTimestampUtc(): string
    {
        return $this->timestamp_utc;
    }

    /**
     * @param string $timestamp_utc
     */
    public function setTimestampUtc(string $timestamp_utc): void
    {
        $this->timestamp_utc = $timestamp_utc;
    }

    /**
     * @return BlockTxMapper[]
     */
    public function getTxs(): array
    {
        return $this->txs;
    }

    /**
     * @param BlockTxMapper[] $txs
     */
    public function setTxs(array $txs): void
    {
        $this->txs = $txs;
    }
}
