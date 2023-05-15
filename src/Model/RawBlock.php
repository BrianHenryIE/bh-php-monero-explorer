<?php

namespace BrianHenryIE\MoneroExplorer\Model;

interface RawBlock
{
    /**
      *
      */
    public function getMajorVersion(): int;

    /**
      *
      */
    public function getMinerTx(): RawBlockMinerTx;

    /**
      *
      */
    public function getMinorVersion(): int;

    /**
      *
      */
    public function getNonce(): int;

    /**
      *
      */
    public function getPrevId(): string;

    /**
      *
      */
    public function getTimestamp(): int;

    /**
     * @return string[]
     */
    public function getTxHashes(): array;
}
