<?php

namespace FMS\Core\DataTransferObjects;

class SaveFundDTO
{
    public function __construct(
        public readonly string $name,
        public readonly int $startYear,
        public readonly int $managerId,
        public readonly ?int $id = null,
    ) {
    }
}
