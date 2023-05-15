<?php

namespace BrianHenryIE\MoneroExplorer\Model\JsonMapper;

use BrianHenryIE\MoneroExplorer\Model\Transaction;

class TransactionMapper implements Transaction
{
    protected bool $coinbase;
    protected string $extra;
    protected int $mixin;
    protected string $paymentId;
    protected string $paymentId8;
    protected int $rctType;
    protected int $txFee;
    protected string $txHash;
    protected int $txSize;
    protected int $txVersion;
    protected int $xmrInputs;
    protected int $xmrOutputs;

    /**
     * @inheritDoc
     */
    public function isCoinbase(): bool
    {
        return $this->coinbase;
    }

    /**
     * @inheritDoc
     */
    public function getExtra(): string
    {
        return $this->extra;
    }

    /**
     * @inheritDoc
     */
    public function getMixin(): int
    {
        return $this->mixin;
    }

    /**
     * @inheritDoc
     */
    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    /**
     * @inheritDoc
     */
    public function getPaymentId8(): string
    {
        return $this->paymentId8;
    }

    /**
     * @inheritDoc
     */
    public function getRctType(): int
    {
        return $this->rctType;
    }

    /**
     * @inheritDoc
     */
    public function getTxFee(): int
    {
        return $this->txFee;
    }

    /**
     * @inheritDoc
     */
    public function getTxHash(): string
    {
        return $this->txHash;
    }

    /**
     * @inheritDoc
     */
    public function getTxSize(): int
    {
        return $this->txSize;
    }

    /**
     * @inheritDoc
     */
    public function getTxVersion(): int
    {
        return $this->txVersion;
    }

    /**
     * @inheritDoc
     */
    public function getXmrInputs(): int
    {
        return $this->xmrInputs;
    }

    /**
     * @inheritDoc
     */
    public function getXmrOutputs(): int
    {
        return $this->xmrOutputs;
    }
    /**
      *  @param bool $coinbase
      */
    public function setCoinbase(bool $coinbase): void
    {
        $this->coinbase = $coinbase;
    }

    /**
      *  @param string $extra
      */
    public function setExtra(string $extra): void
    {
        $this->extra = $extra;
    }

    /**
      *  @param int $mixin
      */
    public function setMixin(int $mixin): void
    {
        $this->mixin = $mixin;
    }

    /**
      *  @param string $paymentId
      */
    public function setPaymentId(string $paymentId): void
    {
        $this->paymentId = $paymentId;
    }

    /**
      *  @param string $paymentId8
      */
    public function setPaymentId8(string $paymentId8): void
    {
        $this->paymentId8 = $paymentId8;
    }

    /**
      *  @param int $rctType
      */
    public function setRctType(int $rctType): void
    {
        $this->rctType = $rctType;
    }

    /**
      *  @param int $txFee
      */
    public function setTxFee(int $txFee): void
    {
        $this->txFee = $txFee;
    }

    /**
      *  @param string $txHash
      */
    public function setTxHash(string $txHash): void
    {
        $this->txHash = $txHash;
    }

    /**
      *  @param int $txSize
      */
    public function setTxSize(int $txSize): void
    {
        $this->txSize = $txSize;
    }

    /**
      *  @param int $txVersion
      */
    public function setTxVersion(int $txVersion): void
    {
        $this->txVersion = $txVersion;
    }

    /**
      *  @param int $xmrInputs
      */
    public function setXmrInputs(int $xmrInputs): void
    {
        $this->xmrInputs = $xmrInputs;
    }

    /**
      *  @param int $xmrOutputs
      */
    public function setXmrOutputs(int $xmrOutputs): void
    {
        $this->xmrOutputs = $xmrOutputs;
    }
}
