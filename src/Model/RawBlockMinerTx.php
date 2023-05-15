<?php

namespace BrianHenryIE\MoneroExplorer\Model;

interface RawBlockMinerTx
{
    /**
     * @return int[]
     */
    public function getExtra(): array;

    /**
      *
      */
    public function getRctSignatures(): \stdClass;

    /**
      *
      */
    public function getUnlockTime(): int;

    /**
      *
      */
    public function getVersion(): int;

    /**
     * @return array
     */
    public function getVin(): array;

    /**
     * @return array
     */
    public function getVout(): array;
}
