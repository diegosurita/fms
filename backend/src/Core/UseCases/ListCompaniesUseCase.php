<?php

namespace FMS\Core\UseCases;

use FMS\Core\Contracts\CompanyRepository;

class ListCompaniesUseCase
{
    public function __construct(private readonly CompanyRepository $companyRepository)
    {
    }

    public function execute(?string $filter = null): array
    {
        return $this->companyRepository->list($filter);
    }
}
