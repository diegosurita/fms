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
        if ($saveFundManagerDTO->id !== null && !$this->fundManagerRepository->exists($saveFundManagerDTO->id)) {
            throw new \RuntimeException('Fund manager not found.', 404);
        }

        return $this->fundManagerRepository->update($saveFundManagerDTO);
    }
}
