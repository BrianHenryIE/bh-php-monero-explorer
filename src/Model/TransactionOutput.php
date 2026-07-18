<?php

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class TransactionOutput
{
    public function __construct(
        /**
         * Amount in atomic units; `0` for RingCT outputs, whose amounts are hidden.
         *
         * @var int
         */
        public int $amount,
        /**
         * The one-time (stealth) public key this output is locked to.
         *
         * @var string
         */
        public string $publicKey,
    ) {
    }
}
