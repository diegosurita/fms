<?php

namespace FMS\Core\Contracts;

use FMS\Core\Entities\FundManagerEntity;

interface FundManagerRepository
{
    /**
     * @return FundManagerEntity[]
     */
    public function list(): array;
}
