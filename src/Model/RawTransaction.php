<?php

namespace BrianHenryIE\MoneroExplorer\Model;

use BrianHenryIE\MoneroExplorer\Model\JsonMapper\RawTransactionVinMapper;

interface RawTransaction
{
    /**
     * @return int[]
     */
    public function getExtra(): array;

    /**
     * @return array
     */
    public function getRctSignatures(): array;

    /**
     * @return array
     */
    public function getRctsigPrunable(): array;

    /**
     * @return int
     */
    public function getUnlockTime(): int;

    /**
     * @return int
     */
    public function getVersion(): int;

    /**
     * @return RawTransactionVin[]
     */
    public function getVin(): array;

    /**
     * @return RawTransactionVout[]
     */
    public function getVout(): array;
}
