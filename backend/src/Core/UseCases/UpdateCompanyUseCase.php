<?php

namespace FMS\Core\UseCases;

use FMS\Core\Contracts\CompanyRepository;
use FMS\Core\DataTransferObjects\SaveCompanyDTO;
use FMS\Core\Entities\CompanyEntity;

class UpdateCompanyUseCase
{
    public function __construct(private readonly CompanyRepository $companyRepository)
    {
    }

    public function execute(SaveCompanyDTO $saveCompanyDTO): CompanyEntity
    {
        if ($saveCompanyDTO->id !== null && !$this->companyRepository->exists($saveCompanyDTO->id)) {
            throw new \RuntimeException('Company not found.', 404);
        }

        return $this->companyRepository->update($saveCompanyDTO);
    }
}