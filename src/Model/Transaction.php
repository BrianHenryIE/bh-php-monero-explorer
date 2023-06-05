<?php

/**
*  @phpstan-type Transaction array{coinbase:bool,extra:string,mixin:int,payment_id:string,payment_id8:string,rct_type:int,tx_fee:int,tx_hash:string,tx_size:int,tx_version:int,xmr_inputs:int,xmr_outputs:int}
*/

namespace BrianHenryIE\MoneroExplorer\Model;

interface Transaction extends Search
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
