<?php

namespace FMS\Core\Events;

class DuplicateFundFoundEvent
{
    public function __construct(
        public readonly int $sourceFundId,
        public readonly int $duplicatedFundId,
    ) {
    }
}
