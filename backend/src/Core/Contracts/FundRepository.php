<?php

namespace FMS\Core\Contracts;

use FMS\Core\DataTransferObjects\SaveFundDTO;
use FMS\Core\Entities\FundEntity;

interface FundRepository
{
    /**
     * @return FundEntity[]
     */
    public function list(?string $filter = null): array;

    public function create(SaveFundDTO $saveFundDTO): FundEntity;

    public function findDuplicateFundId(int $fundId): ?int;

    public function registerDuplicatedFund(int $sourceFundId, int $duplicatedFundId): void;

    public function update(SaveFundDTO $saveFundDTO): FundEntity;

    public function delete(int $id): bool;
}
