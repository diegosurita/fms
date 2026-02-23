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
        return $this->fundRepository->update($saveFundDTO);
    }
}
