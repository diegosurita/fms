<?php

namespace FMS\Core\UseCases;

use FMS\Core\Contracts\FundManagerRepository;

class DeleteFundManagerUseCase
{
    public function __construct(private readonly FundManagerRepository $fundManagerRepository)
    {
    }

    public function execute(int $id): void
    {
        $hasActiveFunds = $this->fundManagerRepository->hasActiveFunds($id);

        if ($hasActiveFunds) {
            throw new \RuntimeException('Fund manager has active funds and cannot be deleted.', 409);
        }

        $deleted = $this->fundManagerRepository->delete($id);

        if ($deleted === false) {
            throw new \RuntimeException('Fund manager not found.', 404);
        }
    }
}
