<?php

namespace FMS\Core\UseCases;

use FMS\Core\Contracts\FundManagerRepository;
use FMS\Core\DataTransferObjects\SaveFundManagerDTO;
use FMS\Core\Entities\FundManagerEntity;

class CreateFundManagerUseCase
{
    public function __construct(private readonly FundManagerRepository $fundManagerRepository)
    {
    }

    public function execute(SaveFundManagerDTO $saveFundManagerDTO): FundManagerEntity
    {
        try {
            return $this->fundManagerRepository->create($saveFundManagerDTO);
        } catch (\Throwable $exception) {
            throw new \RuntimeException('Failed to create fund manager.', 0, $exception);
        }
    }
}
