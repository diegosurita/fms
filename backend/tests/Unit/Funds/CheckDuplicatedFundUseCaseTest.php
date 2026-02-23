<?php

use FMS\Core\Contracts\EventDispatcherInterface;
use FMS\Core\Contracts\FundRepository;
use FMS\Core\Events\DuplicateFundFoundEvent;
use FMS\Core\UseCases\CheckDuplicatedFundUseCase;

test('it dispatches duplicate fund found event when duplicate exists', function () {
    $fundId = 10;
    $duplicatedFundId = 20;

    $repository = \Mockery::mock(FundRepository::class);
    $repository->shouldReceive('findDuplicateFundId')
        ->once()
        ->with($fundId)
        ->andReturn($duplicatedFundId);

    $eventDispatcher = \Mockery::mock(EventDispatcherInterface::class);
    $eventDispatcher->shouldReceive('dispatch')
        ->once()
        ->with(\Mockery::on(function (object $event) use ($fundId, $duplicatedFundId): bool {
            return $event instanceof DuplicateFundFoundEvent
                && $event->sourceFundId === $fundId
                && $event->duplicatedFundId === $duplicatedFundId;
        }));

    $useCase = new CheckDuplicatedFundUseCase($repository, $eventDispatcher);

    $useCase->execute($fundId);

    expect(true)->toBeTrue();
});

test('it does not dispatch event when duplicate does not exist', function () {
    $fundId = 10;

    $repository = \Mockery::mock(FundRepository::class);
    $repository->shouldReceive('findDuplicateFundId')
        ->once()
        ->with($fundId)
        ->andReturn(null);

    $eventDispatcher = \Mockery::mock(EventDispatcherInterface::class);
    $eventDispatcher->shouldNotReceive('dispatch');

    $useCase = new CheckDuplicatedFundUseCase($repository, $eventDispatcher);

    $useCase->execute($fundId);

    expect(true)->toBeTrue();
});
