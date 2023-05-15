<?php

namespace BrianHenryIE\MoneroExplorer\Model;

interface Emission
{
    /**
      *
      */
    public function getBlkNo(): int;

    /**
      *
      */
    public function getCoinbase(): float;

    /**
      *
      */
    public function getFee(): int;
}
