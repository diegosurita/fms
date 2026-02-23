<?php

namespace FMS\Infrastructure\Adapter;

use FMS\Core\Entities\CompanyEntity;

class LaravelCompanyEntityAdapter
{
    public static function fromDB(array $data): CompanyEntity
    {
        $entity = new CompanyEntity();
        $entity->setId(isset($data['id']) ? (int) $data['id'] : null);
        $entity->setName((string) $data['name']);
        $entity->setCreatedAt(isset($data['created_at']) ? new \DateTimeImmutable((string) $data['created_at']) : null);
        $entity->setUpdatedAt(isset($data['updated_at']) ? new \DateTimeImmutable((string) $data['updated_at']) : null);

        return $entity;
    }
}
