<?php

namespace FMS\Core\UseCases;

use FMS\Core\Contracts\FundRepository;
use FMS\Core\DataTransferObjects\DuplicatedFundsDTO;

class GetDuplicatedFundsUseCase
{
    public function __construct(private readonly FundRepository $fundRepository)
    {
    }

    /**
     * @return DuplicatedFundsDTO[]
     */
    public function execute(): array
    {
        return $this->fundRepository->getDuplicated();
    }
}
