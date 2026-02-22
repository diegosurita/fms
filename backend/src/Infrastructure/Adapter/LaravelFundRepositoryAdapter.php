<?php

namespace FMS\Infrastructure\Adapter;

use FMS\Core\Entities\FundEntity;

class LaravelFundRepositoryAdapter
{
    public static function fromDB(array $data): FundEntity
    {
        $entity = new FundEntity();
        $entity->setId(isset($data['id']) ? (int) $data['id'] : null);
        $entity->setName((string) $data['name']);
        $entity->setStartYear((int) $data['start_year']);
        $entity->setManagerId((int) $data['manager_id']);
        $entity->setCreatedAt(isset($data['created_at']) ? new \DateTimeImmutable((string) $data['created_at']) : null);
        $entity->setUpdatedAt(isset($data['updated_at']) ? new \DateTimeImmutable((string) $data['updated_at']) : null);

        return $entity;
    }
}