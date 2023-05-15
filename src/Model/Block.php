<?php

namespace BrianHenryIE\MoneroExplorer\Model;

interface Block
{
    /**
      *
      */
    public function getBlockHeight(): int;

    /**
      *
      */
    public function getCurrentHeight(): int;

    /**
      *
      */
    public function getHash(): string;

    /**
      *
      */
    public function getSize(): int;

    /**
      *
      */
    public function getTimestamp(): int;

    /**
      *
      */
    public function getTimestampUtc(): string;

    /**
     * @return BlockTx[]
     */
    public function getTxs(): array;
}
