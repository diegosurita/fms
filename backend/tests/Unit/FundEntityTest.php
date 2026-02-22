<?php

use FMS\Core\Entities\FundEntity;

test('it stores and returns all fund entity attributes through getters and setters', function () {
    $createdAt = new DateTimeImmutable('2024-01-10 12:00:00');
    $updatedAt = new DateTimeImmutable('2024-01-11 12:00:00');

    $entity = new FundEntity();
    $entity->setId(7);
    $entity->setName('Growth Fund');
    $entity->setStartYear(2018);
    $entity->setManager(3);
    $entity->setCreatedAt($createdAt);
    $entity->setUpdatedAt($updatedAt);

    expect($entity->getId())->toBe(7)
        ->and($entity->getName())->toBe('Growth Fund')
        ->and($entity->getStartYear())->toBe(2018)
        ->and($entity->getManager())->toBe(3)
        ->and($entity->getCreatedAt())->toBe($createdAt)
        ->and($entity->getUpdatedAt())->toBe($updatedAt);
});

test('it allows nullable fields for id and timestamps', function () {
    $entity = new FundEntity();

    $entity->setId(null);
    $entity->setCreatedAt(null);
    $entity->setUpdatedAt(null);

    expect($entity->getId())->toBeNull()
        ->and($entity->getCreatedAt())->toBeNull()
        ->and($entity->getUpdatedAt())->toBeNull();
});
