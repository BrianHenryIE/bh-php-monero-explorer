<?php

namespace BrianHenryIE\MoneroExplorer\Model;

interface Mempool
{
    /**
      *
      */
    public function getLimit(): int;

    /**
      *
      */
    public function getPage(): int;

    /**
      *
      */
    public function getTotalPageNo(): int;

    /**
     * @return MempoolTxs[]
     */
    public function getTxs(): array;

    /**
      *
      */
    public function getTxsNo(): int;
}
