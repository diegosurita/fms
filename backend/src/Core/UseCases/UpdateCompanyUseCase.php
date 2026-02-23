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
        try {
            return $this->companyRepository->update($saveCompanyDTO);
        } catch (\Throwable $exception) {
            throw new \RuntimeException('Failed to update company.', 0, $exception);
        }
    }
}