<?php

namespace FMS\Infrastructure\Listeners;

use FMS\Core\Events\DuplicateFundFoundEvent;
use FMS\Core\UseCases\RegisterDuplicatedFundUseCase;
use Illuminate\Support\Facades\Log;

class DuplicatedFundListener
{
    public function __construct(private readonly RegisterDuplicatedFundUseCase $registerDuplicatedFundUseCase)
    {
    }

    public function handle(DuplicateFundFoundEvent $event): void
    {
        $this->registerDuplicatedFundUseCase->execute($event->sourceFundId, $event->duplicatedFundId);

        Log::warning('Duplicated fund found for same manager.', [
            'source_fund_id' => $event->sourceFundId,
            'duplicated_fund_id' => $event->duplicatedFundId,
        ]);
    }
}
