<?php

namespace FMS\Core\DataTransferObjects;

class SaveFundManagerDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?int $id = null,
    ) {
    }
}
