<?php

use FMS\Core\Contracts\FundRepository;
use FMS\Core\DataTransferObjects\CreateFundDTO;
use FMS\Core\Entities\FundEntity;
use FMS\Core\UseCases\CreateFundUseCase;

test('it delegates to fund repository and returns created fund', function () {
    $createFundDTO = new CreateFundDTO('Alpha Fund', 2022, 10);

    $createdFund = new FundEntity();
    $createdFund->setId(1);
    $createdFund->setName('Alpha Fund');
    $createdFund->setStartYear(2022);
    $createdFund->setManagerId(10);

    $repository = \Mockery::mock(FundRepository::class);
    $repository->shouldReceive('create')
        ->once()
        ->with($createFundDTO)
        ->andReturn($createdFund);

    $useCase = new CreateFundUseCase($repository);

    $result = $useCase->execute($createFundDTO);

    expect($result)->toBe($createdFund);
});

test('it wraps repository exception when creating fund', function () {
    $createFundDTO = new CreateFundDTO('Alpha Fund', 2022, 10);

    $previousException = new RuntimeException('database is unavailable');

    $repository = \Mockery::mock(FundRepository::class);
    $repository->shouldReceive('create')
        ->once()
        ->with($createFundDTO)
        ->andThrow($previousException);

    $useCase = new CreateFundUseCase($repository);

    try {
        $useCase->execute($createFundDTO);
        $this->fail('Expected RuntimeException was not thrown.');
    } catch (RuntimeException $exception) {
        expect($exception->getMessage())->toBe('Failed to create fund.')
            ->and($exception->getCode())->toBe(0)
            ->and($exception->getPrevious())->toBe($previousException);
    }
});
