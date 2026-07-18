<?php

namespace BrianHenryIE\MoneroExplorer\Model;

/**
 * One ring member (possible source output) of a transaction input.
 *
 * Only one ring member is the real output being spent; observers cannot tell which.
 */
final readonly class TransactionInputMixin
{
    public function __construct(
        /**
         * Height of the block containing the ring member output.
         *
         * @var int
         */
        public int $blockNo,
        /**
         * @var string
         */
        public string $publicKey,
        /**
         * Hash of the transaction which created the ring member output.
         *
         * @var string
         */
        public string $txHash,
    ) {
    }
}
