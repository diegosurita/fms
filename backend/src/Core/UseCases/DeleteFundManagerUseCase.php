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
        $deleted = $this->fundManagerRepository->delete($id);

        if ($deleted === false) {
            throw new \RuntimeException('Fund manager not found.', 404);
        }
    }
}
