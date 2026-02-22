<?php

namespace FMS\Core\DataTransferObjects;

class CreateFundDTO
{
    public function __construct(
        public readonly string $name,
        public readonly int $startYear,
        public readonly int $managerId,
    ) {
    }
}
