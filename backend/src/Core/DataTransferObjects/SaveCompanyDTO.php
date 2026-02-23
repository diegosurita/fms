<?php

namespace FMS\Core\DataTransferObjects;

class SaveCompanyDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?int $id = null,
    ) {
    }
}
