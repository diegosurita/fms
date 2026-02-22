<?php

namespace FMS\Core\UseCases;

use FMS\Core\Contracts\FundRepository;
use FMS\Core\DataTransferObjects\CreateFundDTO;
use FMS\Core\Entities\FundEntity;

class CreateFundUseCase
{
    public function __construct(private readonly FundRepository $fundRepository)
    {
    }

    public function execute(CreateFundDTO $createFundDTO): FundEntity
    {
        try {
            return $this->fundRepository->create($createFundDTO);
        } catch (\Throwable $exception) {
            throw new \RuntimeException('Failed to create fund.', 0, $exception);
        }
    }
}
