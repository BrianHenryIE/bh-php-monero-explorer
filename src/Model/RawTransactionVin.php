<?php

/**
 * @package brianhenryie/bh-php-monero-explorer
 */

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class RawTransactionVin
{
    public function __construct(
        /**
         * @var RawTransactionVinKey
         */
        public RawTransactionVinKey $key,
    ) {
    }
}
