<?php

namespace BrianHenryIE\MoneroExplorer\Model;

interface RawTransactionVout
{
    public function getAmount(): int;

    public function getTarget(): RawTransactionVoutTarget;
}
