<?php

/**
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class RawTransactionVoutTarget
{
    public function __construct(
        /**
         * The one-time (stealth) public key this output is locked to.
         *
         * @var string
         */
        public string $key,
    ) {
    }
}
