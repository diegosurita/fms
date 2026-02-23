<?php

namespace FMS\Core\UseCases;

use FMS\Core\Contracts\CompanyRepository;
use FMS\Core\DataTransferObjects\SaveCompanyDTO;
use FMS\Core\Entities\CompanyEntity;

class CreateCompanyUseCase
{
    public function __construct(private readonly CompanyRepository $companyRepository)
    {
    }

    public function execute(SaveCompanyDTO $saveCompanyDTO): CompanyEntity
    {
        try {
            return $this->companyRepository->create($saveCompanyDTO);
        } catch (\Throwable $exception) {
            throw new \RuntimeException('Failed to create company.', 0, $exception);
        }
    }
}
