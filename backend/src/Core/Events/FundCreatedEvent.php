<?php

namespace FMS\Core\Events;

use FMS\Core\Entities\FundEntity;

class FundCreatedEvent
{
    public function __construct(
        public readonly FundEntity $fund,
    ) {
    }
}
