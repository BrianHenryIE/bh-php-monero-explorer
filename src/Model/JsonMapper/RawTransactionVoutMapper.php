<?php

namespace BrianHenryIE\MoneroExplorer\Model\JsonMapper;

use BrianHenryIE\MoneroExplorer\Model\RawTransactionVout;
use BrianHenryIE\MoneroExplorer\Model\RawTransactionVoutTarget;

class RawTransactionVoutMapper implements RawTransactionVout
{
    protected int $amount;

    /** @var RawTransactionVoutTargetMapper $target  */
    protected RawTransactionVoutTarget $target;

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return RawTransactionVoutTarget
     */
    public function getTarget(): RawTransactionVoutTarget
    {
        return $this->target;
    }

    /**
     * @param int $amount
     */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @param RawTransactionVoutTargetMapper $target
     */
    public function setTarget(RawTransactionVoutTarget $target): void
    {
        $this->target = $target;
    }
}
