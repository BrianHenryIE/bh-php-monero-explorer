<?php

namespace BrianHenryIE\MoneroExplorer\Model;

interface Transactions
{
    /**
     * @return TransactionsBlock[]
     */
    public function getBlocks(): array;

    public function getCurrentHeight(): int;

    public function getLimit(): int;

    public function getPage(): int;

    public function getTotalPageNo(): int;
}
