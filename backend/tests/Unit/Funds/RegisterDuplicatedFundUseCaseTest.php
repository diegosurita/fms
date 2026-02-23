<?php

use FMS\Core\Contracts\FundRepository;
use FMS\Core\UseCases\RegisterDuplicatedFundUseCase;

test('it delegates duplicated fund registration to repository', function () {
    $sourceFundId = 10;
    $duplicatedFundId = 20;

    $repository = \Mockery::mock(FundRepository::class);
    $repository->shouldReceive('registerDuplicatedFund')
        ->once()
        ->with($sourceFundId, $duplicatedFundId);

    $useCase = new RegisterDuplicatedFundUseCase($repository);

    $useCase->execute($sourceFundId, $duplicatedFundId);

    expect(true)->toBeTrue();
});

test('it wraps repository exception when registering duplicated fund', function () {
    $sourceFundId = 10;
    $duplicatedFundId = 20;
    $previousException = new RuntimeException('database is unavailable');

    $repository = \Mockery::mock(FundRepository::class);
    $repository->shouldReceive('registerDuplicatedFund')
        ->once()
        ->with($sourceFundId, $duplicatedFundId)
        ->andThrow($previousException);

    $useCase = new RegisterDuplicatedFundUseCase($repository);

    try {
        $useCase->execute($sourceFundId, $duplicatedFundId);
        $this->fail('Expected RuntimeException was not thrown.');
    } catch (RuntimeException $exception) {
        expect($exception->getMessage())->toBe('Failed to register duplicated fund.')
            ->and($exception->getCode())->toBe(0)
            ->and($exception->getPrevious())->toBe($previousException);
    }
});
