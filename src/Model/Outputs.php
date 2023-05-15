<?php

/**
 * Class returned from /api/outputs
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer\Model;

use BrianHenryIE\MoneroExplorer\ExplorerApi;

/**
 * @see ExplorerApi::getOutputs()
 */
interface Outputs
{
    /**
     * @return string
     */
    public function getAddress(): string;

    /**
     * @return OutputsOutput[]
     */
    public function getOutputs(): array;

    /**
     * @return int
     */
    public function getTxConfirmations(): int;

    /**
     * @return string
     */
    public function getTxHash(): string;

    /**
     * @return bool
     */
    public function isTxProve(): bool;

    /**
     * @return int
     */
    public function getTxTimestamp(): int;

    /**
     * @return string
     */
    public function getViewkey(): string;
}
