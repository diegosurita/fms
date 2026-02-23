<?php

namespace FMS\Infrastructure\Adapter;

use FMS\Core\DataTransferObjects\DuplicatedFundsDTO;

class LaravelDuplicatedFundsEntityAdapter
{
    public static function fromDB(array $data): DuplicatedFundsDTO
    {
        $source = LaravelFundRepositoryAdapter::fromDB([
            'id' => $data['source_id'] ?? null,
            'name' => $data['source_name'] ?? null,
            'start_year' => $data['source_start_year'] ?? null,
            'manager_id' => $data['source_manager_id'] ?? null,
            'created_at' => $data['source_created_at'] ?? null,
            'updated_at' => $data['source_updated_at'] ?? null,
        ]);

        $duplicated = LaravelFundRepositoryAdapter::fromDB([
            'id' => $data['duplicated_id'] ?? null,
            'name' => $data['duplicated_name'] ?? null,
            'start_year' => $data['duplicated_start_year'] ?? null,
            'manager_id' => $data['duplicated_manager_id'] ?? null,
            'created_at' => $data['duplicated_created_at'] ?? null,
            'updated_at' => $data['duplicated_updated_at'] ?? null,
        ]);

        return new DuplicatedFundsDTO($source, $duplicated);
    }
}