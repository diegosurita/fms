<?php

namespace FMS\Core\Contracts;

use FMS\Core\DataTransferObjects\SaveCompanyDTO;
use FMS\Core\Entities\CompanyEntity;

interface CompanyRepository
{
    /**
     * @return CompanyEntity[]
     */
    public function list(?string $filter = null): array;

    public function create(SaveCompanyDTO $saveCompanyDTO): CompanyEntity;

    public function update(SaveCompanyDTO $saveCompanyDTO): CompanyEntity;

    public function delete(int $id): bool;
}
