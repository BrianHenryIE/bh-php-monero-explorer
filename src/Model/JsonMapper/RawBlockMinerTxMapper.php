<?php

namespace BrianHenryIE\MoneroExplorer\Model\JsonMapper;

use BrianHenryIE\MoneroExplorer\Model\RawBlockMinerTx;

class RawBlockMinerTxMapper implements RawBlockMinerTx
{
    /** @var int[] */
    protected array $extra;

    /** @var \stdClass{type:int} */
    protected $rctSignatures;
    protected int $unlockTime;
    protected int $version;

    /**
     *  ": [
     *  {
     *  "gen": {
     *  "height": 2676047
     *  }
     *  }
     *  ],
     */
    protected array $vin;

//{
//"amount": 600276950000,
//"target": {
//"key": "aa30cf6acef2d577426ac444e7338af5d44097d5fea414e0bab87142326f867b"
//}
//}
    protected array $vout;

    /**
     *  @inheritDoc
     * @return int[]
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * @inheritDoc
     * TODO
     */
    public function getRctSignatures(): \stdClass
    {
        return $this->rctSignatures;
    }

    /**
     * @inheritDoc
     */
    public function getUnlockTime(): int
    {
        return $this->unlockTime;
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     *  @inheritDoc
     */
    public function getVin(): array
    {
        return $this->vin;
    }

    /**
     *  @inheritDoc
     */
    public function getVout(): array
    {
        return $this->vout;
    }

    /**
      *  @param array $extra
      */
    public function setExtra(array $extra): void
    {
        $this->extra = $extra;
    }

    /**
      *  @param \stdClass $rctSignatures
      */
    public function setRctSignatures(\stdClass $rctSignatures): void
    {
        $this->rctSignatures = $rctSignatures;
    }

    /**
      *  @param int $unlockTime
      */
    public function setUnlockTime(int $unlockTime): void
    {
        $this->unlockTime = $unlockTime;
    }

    /**
      *  @param int $version
      */
    public function setVersion(int $version): void
    {
        $this->version = $version;
    }

    /**
      *  @param array $vin
      */
    public function setVin(array $vin): void
    {
        $this->vin = $vin;
    }

    /**
      *  @param array $vout
      */
    public function setVout(array $vout): void
    {
        $this->vout = $vout;
    }
}
