<?php

namespace FMS\Core\UseCases;

use FMS\Core\Contracts\FundRepository;

class RegisterDuplicatedFundUseCase
{
    public function __construct(private readonly FundRepository $fundRepository)
    {
    }

    public function execute(int $sourceFundId, int $duplicatedFundId): void
    {
        $this->fundRepository->registerDuplicatedFund($sourceFundId, $duplicatedFundId);
    }
}
