<?php

namespace FMS\Core\DataTransferObjects;

use FMS\Core\Entities\FundEntity;

class DuplicatedFundsDTO
{
    public function __construct(
        public readonly FundEntity $source,
        public readonly FundEntity $duplicated,
    ) {
    }
}