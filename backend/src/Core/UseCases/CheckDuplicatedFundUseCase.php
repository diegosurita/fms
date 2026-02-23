<?php

namespace FMS\Core\UseCases;

use FMS\Core\Contracts\EventDispatcherInterface;
use FMS\Core\Contracts\FundRepository;
use FMS\Core\Events\DuplicateFundFoundEvent;

class CheckDuplicatedFundUseCase
{
    public function __construct(
        private readonly FundRepository $fundRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function execute(int $fundId): void
    {
        $duplicatedFundId = $this->fundRepository->findDuplicateFundId($fundId);

        if ($duplicatedFundId === null) {
            return;
        }

        $this->eventDispatcher->dispatch(new DuplicateFundFoundEvent($fundId, $duplicatedFundId));
    }
}
