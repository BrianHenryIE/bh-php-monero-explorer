<?php

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class TransactionInput
{
    /**
     * @param TransactionInputMixin[] $mixins
     */
    public function __construct(
        /**
         * Amount in atomic units; `0` for RingCT inputs, whose amounts are hidden.
         *
         * @var int
         */
        public int $amount,
        /**
         * The input's key image.
         *
         * @var string
         */
        public string $keyImage,
        /**
         * @var TransactionInputMixin[]
         */
        public array $mixins,
    ) {
    }
}
