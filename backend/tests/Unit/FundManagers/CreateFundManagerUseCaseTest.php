<?php

use FMS\Core\Contracts\FundManagerRepository;
use FMS\Core\DataTransferObjects\SaveFundManagerDTO;
use FMS\Core\Entities\FundManagerEntity;
use FMS\Core\UseCases\CreateFundManagerUseCase;

test('it delegates to fund manager repository and returns created fund manager', function () {
    $saveFundManagerDTO = new SaveFundManagerDTO('Alpha Manager');

    $createdFundManager = new FundManagerEntity();
    $createdFundManager->setId(1);
    $createdFundManager->setName('Alpha Manager');

    $repository = \Mockery::mock(FundManagerRepository::class);
    $repository->shouldReceive('create')
        ->once()
        ->with($saveFundManagerDTO)
        ->andReturn($createdFundManager);

    $useCase = new CreateFundManagerUseCase($repository);

    $result = $useCase->execute($saveFundManagerDTO);

    expect($result)->toBe($createdFundManager);
});

test('it wraps repository exception when creating fund manager', function () {
    $saveFundManagerDTO = new SaveFundManagerDTO('Alpha Manager');

    $previousException = new RuntimeException('database is unavailable');

    $repository = \Mockery::mock(FundManagerRepository::class);
    $repository->shouldReceive('create')
        ->once()
        ->with($saveFundManagerDTO)
        ->andThrow($previousException);

    $useCase = new CreateFundManagerUseCase($repository);

    try {
        $useCase->execute($saveFundManagerDTO);
        $this->fail('Expected RuntimeException was not thrown.');
    } catch (RuntimeException $exception) {
        expect($exception->getMessage())->toBe('Failed to create fund manager.')
            ->and($exception->getCode())->toBe(0)
            ->and($exception->getPrevious())->toBe($previousException);
    }
});
