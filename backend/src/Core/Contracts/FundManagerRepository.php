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

    public function update(SaveFundManagerDTO $saveFundManagerDTO): FundManagerEntity;

    public function exists(int $id): bool;

    public function hasActiveFunds(int $id): bool;

    public function delete(int $id): bool;
}
