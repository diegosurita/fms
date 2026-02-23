<?php

namespace FMS\Infrastructure\Adapter;

use FMS\Core\Entities\FundManagerEntity;

class LaravelFundManagerEntityAdapter
{
    public static function fromDB(array $data): FundManagerEntity
    {
        $entity = new FundManagerEntity();
        $entity->setId(isset($data['id']) ? (int) $data['id'] : null);
        $entity->setName((string) $data['name']);
        $entity->setCreatedAt(isset($data['created_at']) ? new \DateTimeImmutable((string) $data['created_at']) : null);
        $entity->setUpdatedAt(isset($data['updated_at']) ? new \DateTimeImmutable((string) $data['updated_at']) : null);

        return $entity;
    }
}
