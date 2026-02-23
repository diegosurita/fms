<?php

namespace FMS\Core\Contracts;

use FMS\Core\DataTransferObjects\SaveFundManagerDTO;
use FMS\Core\Entities\FundManagerEntity;

interface FundManagerRepository
{
    /**
     * @return FundManagerEntity[]
     */
    public function list(): array;

    public function create(SaveFundManagerDTO $saveFundManagerDTO): FundManagerEntity;
}
