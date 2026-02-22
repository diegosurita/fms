<?php

namespace FMS\Core\UseCases;

use FMS\Core\Contracts\FundRepository;

class DeleteFundUseCase
{
    public function __construct(private readonly FundRepository $fundRepository)
    {
    }

    public function execute(int $id): void
    {
        $deleted = $this->fundRepository->delete($id);

        if ($deleted === false) {
            throw new \RuntimeException('Fund not found.', 404);
        }
    }
}
