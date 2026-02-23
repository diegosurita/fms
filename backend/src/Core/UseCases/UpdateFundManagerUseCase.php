<?php

namespace FMS\Core\UseCases;

use FMS\Core\Contracts\FundManagerRepository;
use FMS\Core\DataTransferObjects\SaveFundManagerDTO;
use FMS\Core\Entities\FundManagerEntity;

class UpdateFundManagerUseCase
{
    public function __construct(private readonly FundManagerRepository $fundManagerRepository)
    {
    }

    public function execute(SaveFundManagerDTO $saveFundManagerDTO): FundManagerEntity
    {
        try {
            return $this->fundManagerRepository->update($saveFundManagerDTO);
        } catch (\Throwable $exception) {
            throw new \RuntimeException('Failed to update fund manager.', 0, $exception);
        }
    }
}
