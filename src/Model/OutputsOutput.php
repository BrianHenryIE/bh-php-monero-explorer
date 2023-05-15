<?php

/**
 * Property in class returned from /api/outputs
 *
 * @see Outputs
 *
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer\Model;

interface OutputsOutput
{
    /**
     * @return int
     */
    public function getAmount(): int;

    public function isMatch(): bool;

    /**
     * @return int
     */
    public function getOutputIdx(): int;

    /**
     * @return string
     */
    public function getOutputPubkey(): string;
}
