<?php

namespace BrianHenryIE\MoneroExplorer\Model;

interface RawTransactionVinKey
{
    public function getAmount(): int;

    public function getKImage(): string;

    /**
     * @return int[]
     */
    public function getKeyOffsets(): array;
}
