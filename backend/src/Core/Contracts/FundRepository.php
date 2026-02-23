<?php

namespace FMS\Core\Contracts;

use FMS\Core\DataTransferObjects\DuplicatedFundsDTO;
use FMS\Core\DataTransferObjects\SaveFundDTO;
use FMS\Core\Entities\FundEntity;

interface FundRepository extends RepositoryTransactionInterface
{
    /**
     * @return FundEntity[]
     */
    public function list(?string $filter = null): array;

    public function create(SaveFundDTO $saveFundDTO): FundEntity;

    public function findDuplicateFundId(int $fundId): ?int;

    /**
     * @return DuplicatedFundsDTO[]
     */
    public function getDuplicated(): array;

    public function registerDuplicatedFund(int $sourceFundId, int $duplicatedFundId): void;

    public function update(SaveFundDTO $saveFundDTO): FundEntity;

    public function delete(int $id): bool;
}
