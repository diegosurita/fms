<?php

use FMS\Core\Contracts\EventDispatcherInterface;
use FMS\Core\Contracts\FundRepository;
use FMS\Core\DataTransferObjects\SaveFundDTO;
use FMS\Core\Entities\FundEntity;
use FMS\Core\Events\FundCreatedEvent;
use FMS\Core\UseCases\CreateFundUseCase;

test('it delegates to fund repository and returns created fund', function () {
    $saveFundDTO = new SaveFundDTO('Alpha Fund', 2022, 10);

    $createdFund = new FundEntity();
    $createdFund->setId(1);
    $createdFund->setName('Alpha Fund');
    $createdFund->setStartYear(2022);
    $createdFund->setManagerId(10);

    $repository = \Mockery::mock(FundRepository::class);
    $repository->shouldReceive('create')
        ->once()
        ->with($saveFundDTO)
        ->andReturn($createdFund);

    $eventDispatcher = \Mockery::mock(EventDispatcherInterface::class);
    $eventDispatcher->shouldReceive('dispatch')
        ->once()
        ->with(\Mockery::on(function (object $event) use ($createdFund): bool {
            return $event instanceof FundCreatedEvent
                && $event->fund === $createdFund;
        }));

    $useCase = new CreateFundUseCase($repository, $eventDispatcher);

    $result = $useCase->execute($saveFundDTO);

    expect($result)->toBe($createdFund);
});

test('it wraps repository exception when creating fund', function () {
    $saveFundDTO = new SaveFundDTO('Alpha Fund', 2022, 10);

    $previousException = new RuntimeException('database is unavailable');

    $repository = \Mockery::mock(FundRepository::class);
    $repository->shouldReceive('create')
        ->once()
        ->with($saveFundDTO)
        ->andThrow($previousException);

    $eventDispatcher = \Mockery::mock(EventDispatcherInterface::class);
    $eventDispatcher->shouldNotReceive('dispatch');

    $useCase = new CreateFundUseCase($repository, $eventDispatcher);

    try {
        $useCase->execute($saveFundDTO);
        $this->fail('Expected RuntimeException was not thrown.');
    } catch (RuntimeException $exception) {
        expect($exception->getMessage())->toBe('Failed to create fund.')
            ->and($exception->getCode())->toBe(0)
            ->and($exception->getPrevious())->toBe($previousException);
    }
});

test('it propagates invalid argument exception when creating fund', function () {
    $saveFundDTO = new SaveFundDTO('Alpha Fund', 2022, 10, aliases: ['Alpha Alias']);

    $previousException = new InvalidArgumentException('Alias already exists.');

    $repository = \Mockery::mock(FundRepository::class);
    $repository->shouldReceive('create')
        ->once()
        ->with($saveFundDTO)
        ->andThrow($previousException);

    $eventDispatcher = \Mockery::mock(EventDispatcherInterface::class);
    $eventDispatcher->shouldNotReceive('dispatch');

    $useCase = new CreateFundUseCase($repository, $eventDispatcher);

    try {
        $useCase->execute($saveFundDTO);
        $this->fail('Expected InvalidArgumentException was not thrown.');
    } catch (InvalidArgumentException $exception) {
        expect($exception->getMessage())->toBe('Alias already exists.');
    }
});
