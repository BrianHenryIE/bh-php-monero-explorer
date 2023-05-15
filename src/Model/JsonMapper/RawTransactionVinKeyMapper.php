<?php

namespace BrianHenryIE\MoneroExplorer\Model\JsonMapper;

use BrianHenryIE\MoneroExplorer\Model\RawTransactionVinKey;

class RawTransactionVinKeyMapper implements RawTransactionVinKey
{
    protected int $amount;
    protected string $kImage;

    /** @var int[] $keyOffsets */
    protected array $keyOffsets;

    /**
     * @inheritDoc
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @inheritDoc
     */
    public function getKImage(): string
    {
        return $this->kImage;
    }

    /**
     * @inheritDoc
     */
    public function getKeyOffsets(): array
    {
        return $this->keyOffsets;
    }

    /**
     * @param int $amount
     */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @param string $kImage
     */
    public function setKImage(string $kImage): void
    {
        $this->kImage = $kImage;
    }

    /**
     * @param int[] $keyOffsets
     */
    public function setKeyOffsets(array $keyOffsets): void
    {
        $this->keyOffsets = $keyOffsets;
    }
}
