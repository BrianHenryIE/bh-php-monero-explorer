<?php

namespace BrianHenryIE\MoneroExplorer\Model\JsonMapper;

use BrianHenryIE\MoneroExplorer\Model\Mempool;
use BrianHenryIE\MoneroExplorer\Model\MempoolTxs;

class MempoolMapper implements Mempool
{
    /** @var MempoolTxsMapper[] $txs */
    protected array $txs;
    protected int $limit;
    protected int $page;
    protected int $totalPageNo;
    protected int $txsNo;

    /**
     * @inheritDoc
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @inheritDoc
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @inheritDoc
     */
    public function getTotalPageNo(): int
    {
        return $this->totalPageNo;
    }

    /**
     *  @inheritDoc
     *
     * @return MempoolTxs[]
     */
    public function getTxs(): array
    {
        return $this->txs;
    }

    /**
     * @inheritDoc
     */
    public function getTxsNo(): int
    {
        return $this->txsNo;
    }

    /**
      *  @param int $limit
      */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
      *  @param int $page
      */
    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    /**
      *  @param int $totalPageNo
      */
    public function setTotalPageNo(int $totalPageNo): void
    {
        $this->totalPageNo = $totalPageNo;
    }

    /**
      *  @param MempoolTxsMapper[] $txs
      */
    public function setTxs(array $txs): void
    {
        $this->txs = $txs;
    }

    /**
      *  @param int $txsNo
      */
    public function setTxsNo(int $txsNo): void
    {
        $this->txsNo = $txsNo;
    }
}
