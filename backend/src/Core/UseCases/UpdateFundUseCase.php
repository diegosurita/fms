<?php

namespace FMS\Core\UseCases;

use FMS\Core\Contracts\FundRepository;
use FMS\Core\DataTransferObjects\SaveFundDTO;
use FMS\Core\Entities\FundEntity;

class UpdateFundUseCase
{
    public function __construct(private readonly FundRepository $fundRepository)
    {
    }

    public function execute(SaveFundDTO $saveFundDTO): FundEntity
    {
        if ($saveFundDTO->id !== null && !$this->fundRepository->exists($saveFundDTO->id)) {
            throw new \RuntimeException('Fund not found.', 404);
        }

        return $this->fundRepository->update($saveFundDTO);
    }
}
