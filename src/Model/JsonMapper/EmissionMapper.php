<?php

namespace BrianHenryIE\MoneroExplorer\Model\JsonMapper;

use BrianHenryIE\MoneroExplorer\Model\Emission;

class EmissionMapper implements Emission
{
    protected int $blkNo;
    protected float $coinbase;
    protected int $fee;

    /**
     * @inheritDoc
     */
    public function getBlkNo(): int
    {
        return $this->blkNo;
    }

    /**
     * @inheritDoc
     */
    public function getCoinbase(): float
    {
        return $this->coinbase;
    }

    /**
     * @inheritDoc
     */
    public function getFee(): int
    {
        return $this->fee;
    }

    /**
      *  @param int $blkNo
      */
    public function setBlkNo(int $blkNo): void
    {
        $this->blkNo = $blkNo;
    }

    /**
      *  @param float $coinbase
      */
    public function setCoinbase(float $coinbase): void
    {
        $this->coinbase = $coinbase;
    }

    /**
      *  @param int $fee
      */
    public function setFee(int $fee): void
    {
        $this->fee = $fee;
    }
}
