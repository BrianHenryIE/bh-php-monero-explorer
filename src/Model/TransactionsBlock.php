<?php

namespace BrianHenryIE\MoneroExplorer\Model;

interface TransactionsBlock
{
    public function getAge(): string;

    public function getHash(): string;

    public function getHeight(): int;

    public function getSize(): float;

    public function getTimestamp(): int;

    public function getTimestampUtc(): string;

    /**
     * @return BlockTx[]
     */
    public function getTxs(): array;
}
