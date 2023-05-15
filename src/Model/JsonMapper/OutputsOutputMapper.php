<?php

namespace BrianHenryIE\MoneroExplorer\Model\JsonMapper;

use BrianHenryIE\MoneroExplorer\Model\OutputsOutput;

class OutputsOutputMapper implements OutputsOutput
{
    protected int $amount;
    protected bool $match;
    protected int $outputIdx;
    protected string $outputPubkey;

    /**
     * @inheritDoc
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     *  @inheritDoc
     */
    public function isMatch(): bool
    {
        return $this->match;
    }

    /**
     * @inheritDoc
     */
    public function getOutputIdx(): int
    {
        return $this->outputIdx;
    }

    /**
     * @inheritDoc
     */
    public function getOutputPubkey(): string
    {
        return $this->outputPubkey;
    }

    /**
     * @param int $amount
     */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function setMatch(bool $match): void
    {
        $this->match = $match;
    }

    /**
     * @param int $outputIdx
     */
    public function setOutputIdx(int $outputIdx): void
    {
        $this->outputIdx = $outputIdx;
    }

    /**
     * @param string $outputPubkey
     */
    public function setOutputPubkey(string $outputPubkey): void
    {
        $this->outputPubkey = $outputPubkey;
    }
}
