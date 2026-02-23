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
        try {
            return $this->fundRepository->getDuplicated();
        } catch (\Throwable $exception) {
            throw new \RuntimeException('Failed to get duplicated funds.', 0, $exception);
        }
    }
}
