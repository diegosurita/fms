<?php

use FMS\Core\Contracts\FundRepository;
use FMS\Core\Entities\FundEntity;
use FMS\Core\UseCases\ListFundsUseCase;

test('it delegates to fund repository and returns listed funds', function () {
    $fund = new FundEntity();
    $fund->setId(1);
    $fund->setName('Fund 1');
    $fund->setStartYear(2021);
    $fund->setManager(10);

    $expectedFunds = [$fund];

    $repository = \Mockery::mock(FundRepository::class);
    $repository->shouldReceive('list')
        ->once()
        ->with(null)
        ->andReturn($expectedFunds);

    $useCase = new ListFundsUseCase($repository);

    $result = $useCase->execute();

    expect($result)->toBe($expectedFunds);
});

test('it delegates filter to fund repository', function () {
    $repository = \Mockery::mock(FundRepository::class);
    $repository->shouldReceive('list')
        ->once()
        ->with('target')
        ->andReturn([]);

    $useCase = new ListFundsUseCase($repository);

    $result = $useCase->execute('target');

    expect($result)->toBe([]);
});
