<?php

namespace FMS\Infrastructure\Listeners;

use FMS\Core\Events\FundCreatedEvent;
use FMS\Core\UseCases\CheckDuplicatedFundUseCase;

class ValidateUniqueFundListener
{
    public function __construct(
        private readonly CheckDuplicatedFundUseCase $checkDuplicatedFundUseCase,
    ) {
    }

    public function handle(FundCreatedEvent $event): void
    {
        $fundId = $event->fund->getId();

        if ($fundId === null) {
            return;
        }

        $this->checkDuplicatedFundUseCase->execute($fundId);
    }
}
