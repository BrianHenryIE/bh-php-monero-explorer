<?php

namespace BrianHenryIE\MoneroExplorer\Model\JsonMapper;

use BrianHenryIE\MoneroExplorer\Model\OutputsBlocks;
use BrianHenryIE\MoneroExplorer\Model\OutputsBlocksOutput;

class OutputsBlocksMapper implements OutputsBlocks
{
    protected string $address;
    protected int $height;
    protected string $limit;
    protected bool $mempool;

    /** @var OutputsBlocksOutputMapper[] $outputs */
    protected array $outputs;
    protected string $viewkey;

    /**
     * @inheritDoc
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * inheritDoc
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @inheritDoc
     */
    public function getLimit(): string
    {
        return $this->limit;
    }

    /**
     * @inheritDoc
     */
    public function isMempool(): bool
    {
        return $this->mempool;
    }

    /**
     *  @inheritDoc
     */
    public function getOutputs(): array
    {
        return $this->outputs;
    }

    /**
     * @inheritDoc
     */
    public function getViewkey(): string
    {
        return $this->viewkey;
    }
    /**
      *  @param string $address
      */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
      *  @param int $height
      */
    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    /**
      *  @param string $limit
      */
    public function setLimit(string $limit): void
    {
        $this->limit = $limit;
    }

    /**
      *  @param bool $mempool
      */
    public function setMempool(bool $mempool): void
    {
        $this->mempool = $mempool;
    }

    /**
      *  @param OutputsBlocksOutputMapper[] $outputs
      */
    public function setOutputs(array $outputs): void
    {
        $this->outputs = $outputs;
    }

    /**
      *  @param string $viewkey
      */
    public function setViewkey(string $viewkey): void
    {
        $this->viewkey = $viewkey;
    }
}
