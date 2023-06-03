<?php

namespace BrianHenryIE\MoneroExplorer\Model\JsonMapper;

use BrianHenryIE\MoneroExplorer\Model\Transactions;
use BrianHenryIE\MoneroExplorer\Model\TransactionsBlock;

class TransactionsMapper implements Transactions
{
    /**
     * @var TransactionsBlockMapper[]
     */
    protected array $blocks;

    /**
     * The current blockchain height.
     */
    protected int $current_height;

    /**
     * Query parameter.
     */
    protected int $limit;

    /**
     * Query parameter.
     */
    protected int $page;

    protected int $totalPageNo;

    /**
     * @return TransactionsBlockMapper[]
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    /**
     * @param TransactionsBlockMapper[] $blocks
     */
    public function setBlocks(array $blocks): void
    {
        $this->blocks = $blocks;
    }

    /**
     * @return int
     */
    public function getCurrentHeight(): int
    {
        return $this->current_height;
    }

    /**
     * @param int $current_height
     */
    public function setCurrentHeight(int $current_height): void
    {
        $this->current_height = $current_height;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     */
    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    /**
     * @return int
     */
    public function getTotalPageNo(): int
    {
        return $this->totalPageNo;
    }

    /**
     * @param int $totalPageNo
     */
    public function setTotalPageNo(int $totalPageNo): void
    {
        $this->totalPageNo = $totalPageNo;
    }
}
