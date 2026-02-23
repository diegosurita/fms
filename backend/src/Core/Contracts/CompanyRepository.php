<?php

namespace FMS\Core\Contracts;

use FMS\Core\Entities\CompanyEntity;

interface CompanyRepository
{
    /**
     * @return CompanyEntity[]
     */
    public function list(?string $filter = null): array;
}
