<?php

namespace BrianHenryIE\MoneroExplorer\Model\JsonMapper;

use BrianHenryIE\MoneroExplorer\Model\Version;

class VersionMapper implements Version
{
    protected int $api;
    protected int $blockchainHeight;
    protected string $gitBranchName;
    protected string $lastGitCommitDate;
    protected string $lastGitCommitHash;
    protected string $moneroVersionFull;

    /**
     * API number is stored as uint32_t.
     *
     *  @inheritDoc
     */
    public function getApi(): int
    {
        return $this->api;
    }

    /**
     * @inheritDoc
     */
    public function getBlockchainHeight(): int
    {
        return $this->blockchainHeight;
    }

    /**
     * @inheritDoc
     */
    public function getGitBranchName(): string
    {
        return $this->gitBranchName;
    }

    /**
     * @inheritDoc
     */
    public function getLastGitCommitDate(): string
    {
        return $this->lastGitCommitDate;
    }

    /**
     * @inheritDoc
     */
    public function getLastGitCommitHash(): string
    {
        return $this->lastGitCommitHash;
    }

    /**
     * @inheritDoc
     */
    public function getMoneroVersionFull(): string
    {
        return $this->moneroVersionFull;
    }
    /**
      *  @param int $api
      */
    public function setApi(int $api): void
    {
        $this->api = $api;
    }

    /**
      *  @param int $blockchainHeight
      */
    public function setBlockchainHeight(int $blockchainHeight): void
    {
        $this->blockchainHeight = $blockchainHeight;
    }

    /**
      *  @param string $gitBranchName
      */
    public function setGitBranchName(string $gitBranchName): void
    {
        $this->gitBranchName = $gitBranchName;
    }

    /**
      *  @param string $lastGitCommitDate
      */
    public function setLastGitCommitDate(string $lastGitCommitDate): void
    {
        $this->lastGitCommitDate = $lastGitCommitDate;
    }

    /**
      *  @param string $lastGitCommitHash
      */
    public function setLastGitCommitHash(string $lastGitCommitHash): void
    {
        $this->lastGitCommitHash = $lastGitCommitHash;
    }

    /**
      *  @param string $moneroVersionFull
      */
    public function setMoneroVersionFull(string $moneroVersionFull): void
    {
        $this->moneroVersionFull = $moneroVersionFull;
    }
}
