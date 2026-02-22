<?php

namespace FMS\Core\Contracts;

use FMS\Core\DataTransferObjects\CreateFundDTO;
use FMS\Core\Entities\FundEntity;

interface FundRepository
{
    /**
     * @return FundEntity[]
     */
    public function list(?string $filter = null): array;

    public function create(CreateFundDTO $createFundDTO): FundEntity;
}
