<?php

namespace FMS\Core\UseCases;

use FMS\Core\Contracts\CompanyRepository;

class DeleteCompanyUseCase
{
    public function __construct(private readonly CompanyRepository $companyRepository)
    {
    }

    public function execute(int $id): void
    {
        $deleted = $this->companyRepository->delete($id);

        if ($deleted === false) {
            throw new \RuntimeException('Company not found.', 404);
        }
    }
}