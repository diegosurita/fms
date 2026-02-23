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
        try {
            $this->fundRepository->registerDuplicatedFund($sourceFundId, $duplicatedFundId);
        } catch (\Throwable $exception) {
            throw new \RuntimeException('Failed to register duplicated fund.', 0, $exception);
        }
    }
}
