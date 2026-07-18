<?php

/**
 * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/version.h.in
 *
 */

namespace BrianHenryIE\MoneroExplorer\Model;

final readonly class Version
{
    public function __construct(
        /**
         * API number is stored as uint32_t: major version in the first 16 bits,
         * minor in the last 16 (`$major = $api >> 16; $minor = $api & 0xffff;`).
         *
         * @var int
         */
        public int $api,
        /** @var int */
        public int $blockchainHeight,
        /** @var string */
        public string $gitBranchName,
        /** @var string */
        public string $lastGitCommitDate,
        /** @var string */
        public string $lastGitCommitHash,
        /** @var string */
        public string $moneroVersionFull,
    ) {
    }
}
