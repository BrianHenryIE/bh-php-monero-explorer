<?php

/**
      *  Model for the `txs` array in Block objects.
      */

namespace BrianHenryIE\MoneroExplorer\Model;

interface BlockTx
{
    /**
      *
      */
    public function isCoinbase(): bool;

    /**
      *
      */
    public function getExtra(): string;

    /**
      *
      */
    public function getMixin(): int;

    /**
      *
      */
    public function getPaymentId(): string;

    /**
      *
      */
    public function getPaymentId8(): string;

    /**
      *
      */
    public function getRctType(): int;

    /**
      *
      */
    public function getTxFee(): int;

    /**
      *
      */
    public function getTxHash(): string;

    /**
      *
      */
    public function getTxSize(): int;

    /**
      *
      */
    public function getTxVersion(): int;

    /**
      *
      */
    public function getXmrInputs(): int;

    /**
      *
      */
    public function getXmrOutputs(): int;
}
