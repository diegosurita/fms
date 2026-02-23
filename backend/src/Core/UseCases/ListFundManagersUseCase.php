<?php

namespace FMS\Core\UseCases;

use FMS\Core\Contracts\FundManagerRepository;

class ListFundManagersUseCase
{
    public function __construct(private readonly FundManagerRepository $fundManagerRepository)
    {
    }

    public function execute(): array
    {
        return $this->fundManagerRepository->list();
    }
}
