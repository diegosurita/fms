<?php

namespace FMS\Core\UseCases;

use FMS\Core\Contracts\FundRepository;

class ListFundsUseCase
{
    public function __construct(private readonly FundRepository $fundRepository)
    {
    }

    public function execute(?string $filter = null): array
    {
        return $this->fundRepository->list($filter);
    }
}
