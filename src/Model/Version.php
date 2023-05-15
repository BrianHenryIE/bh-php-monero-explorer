<?php

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
