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
        return $this->fundManagerRepository->update($saveFundManagerDTO);
    }
}
