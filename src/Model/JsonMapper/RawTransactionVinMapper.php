<?php

namespace BrianHenryIE\MoneroExplorer\Model\JsonMapper;

use BrianHenryIE\MoneroExplorer\Model\RawTransactionVin;
use BrianHenryIE\MoneroExplorer\Model\RawTransactionVinKey;

class RawTransactionVinMapper implements RawTransactionVin
{
    protected RawTransactionVinKeyMapper $key;

    /**
     *  @inheritDoc
     */
    public function getKey(): RawTransactionVinKey
    {
        return $this->key;
    }

    /**
     * @param RawTransactionVinKeyMapper $key
     */
    public function setKey(RawTransactionVinKey $key): void
    {
        $this->key = $key;
    }
}
