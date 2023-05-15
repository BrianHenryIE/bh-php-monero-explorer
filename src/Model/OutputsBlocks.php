<?php

/**
 * Class returned from /api/outputsblocks
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer\Model;

interface OutputsBlocks
{
    /**
      *
      */
    public function getAddress(): string;

    /**
      *
      */
    public function getHeight(): int;

    /**
      *
      */
    public function getLimit(): string;

    /**
      *
      */
    public function isMempool(): bool;

    /**
      * TODO: test data has empty array.
      *
      * @return OutputsBlocksOutput[]
      */
    public function getOutputs(): array;

    /**
      *
      */
    public function getViewkey(): string;
}
