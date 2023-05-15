<?php

namespace BrianHenryIE\MoneroExplorer\Model\JsonMapper;

use BrianHenryIE\MoneroExplorer\Model\Outputs;
use BrianHenryIE\MoneroExplorer\Model\OutputsOutput;

class OutputsMapper implements Outputs
{
    protected string $address;

    /** @var OutputsOutputMapper[] $outputs */
    protected array $outputs;

    protected int $txConfirmations;
    protected string $txHash;
    protected bool $txProve;
    protected int $txTimestamp;
    protected string $viewkey;

    /**
     *  @inheritDoc
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     *  @inheritDoc
     *
     * @return OutputsOutput[]
     */
    public function getOutputs(): array
    {
        return $this->outputs;
    }

    /**
     *  @inheritDoc
     */
    public function getTxConfirmations(): int
    {
        return $this->txConfirmations;
    }

    /**
     * @inheritDoc
     */
    public function getTxHash(): string
    {
        return $this->txHash;
    }

    /**
     *  @inheritDoc
     */
    public function isTxProve(): bool
    {
        return $this->txProve;
    }

    /**
     * @inheritDoc
     */
    public function getTxTimestamp(): int
    {
        return $this->txTimestamp;
    }

    /**
     * @inheritDoc
     */
    public function getViewkey(): string
    {
        return $this->viewkey;
    }


    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @param OutputsOutputMapper[] $outputs
     */
    public function setOutputs(array $outputs): void
    {
        $this->outputs = $outputs;
    }

    /**
     * @param int $txConfirmations
     */
    public function setTxConfirmations(int $txConfirmations): void
    {
        $this->txConfirmations = $txConfirmations;
    }

    /**
     * @param string $txHash
     */
    public function setTxHash(string $txHash): void
    {
        $this->txHash = $txHash;
    }

    /**
     * @param bool $txProve
     */
    public function setTxProve(bool $txProve): void
    {
        $this->txProve = $txProve;
    }

    /**
     * @param int $txTimestamp
     */
    public function setTxTimestamp(int $txTimestamp): void
    {
        $this->txTimestamp = $txTimestamp;
    }

    /**
     * @param string $viewkey
     */
    public function setViewkey(string $viewkey): void
    {
        $this->viewkey = $viewkey;
    }
}
