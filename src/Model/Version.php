<?php

/**
 * @see https://github.com/moneroexamples/onion-monero-blockchain-explorer/blob/d66972065fd34339451c248b4dfb5c54be0d0719/src/version.h.in
 *
 */

namespace BrianHenryIE\MoneroExplorer\Model;

interface Version
{
    /**
      * API number is stored as uint32_t.
      */
    public function getApi(): int;

    /**
      *
      */
    public function getBlockchainHeight(): int;

    /**
      *
      */
    public function getGitBranchName(): string;

    /**
      *
      */
    public function getLastGitCommitDate(): string;

    /**
      *
      */
    public function getLastGitCommitHash(): string;

    /**
      *
      */
    public function getMoneroVersionFull(): string;
}
