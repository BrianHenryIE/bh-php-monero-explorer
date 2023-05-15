<?php

namespace BrianHenryIE\MoneroExplorer\Model\JsonMapper;

use BrianHenryIE\MoneroExplorer\Model\RawTransaction;
use BrianHenryIE\MoneroExplorer\Model\RawTransactionVout;

class RawTransactionMapper implements RawTransaction
{
    /** @var int[] $extra */
    protected array $extra;

    protected array $rctSignatures;

    // TODO:
    protected array $rctsigPrunable;

    protected int $unlockTime;
    protected int $version;

    /** @var RawTransactionVinMapper[] $vin */
    protected array $vin;

    /** @var RawTransactionVoutMapper[] $vout */
    protected array $vout;

    /**
     *  @inheritDoc
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     *  @inheritDoc
     */
    public function getRctSignatures(): array
    {
        return $this->rctSignatures;
    }

    /**
     *  @inheritDoc
     */
    public function getRctsigPrunable(): array
    {
        return $this->rctsigPrunable;
    }

    /**
     * @inheritDoc
     */
    public function getUnlockTime(): int
    {
        return $this->unlockTime;
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @inheritDoc
     */
    public function getVin(): array
    {
        return $this->vin;
    }

    /**
     * @inheritDoc
     */
    public function getVout(): array
    {
        return $this->vout;
    }

    /**
     * @param int[] $extra
     */
    public function setExtra(array $extra): void
    {
        $this->extra = $extra;
    }

    /**
     * @param array $rctSignatures
     */
    public function setRctSignatures(array $rctSignatures): void
    {
        $this->rctSignatures = $rctSignatures;
    }

    /**
     * @param array $rctsigPrunable
     */
    public function setRctsigPrunable(array $rctsigPrunable): void
    {
        $this->rctsigPrunable = $rctsigPrunable;
    }

    /**
     * @param int $unlockTime
     */
    public function setUnlockTime(int $unlockTime): void
    {
        $this->unlockTime = $unlockTime;
    }

    /**
     * @param int $version
     */
    public function setVersion(int $version): void
    {
        $this->version = $version;
    }

    /**
     * @param RawTransactionVinMapper[] $vin
     */
    public function setVin(array $vin): void
    {
        $this->vin = $vin;
    }

    /**
     * @param RawTransactionVout[] $vout
     */
    public function setVout(array $vout): void
    {
        $this->vout = $vout;
    }
}
