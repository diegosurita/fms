<?php

use FMS\Core\Contracts\FundManagerRepository;
use FMS\Core\DataTransferObjects\SaveFundManagerDTO;
use FMS\Core\Entities\FundManagerEntity;
use FMS\Core\UseCases\UpdateFundManagerUseCase;

test('it delegates to fund manager repository and returns updated fund manager', function () {
    $saveFundManagerDTO = new SaveFundManagerDTO('Updated Manager', 1);

    $updatedFundManager = new FundManagerEntity();
    $updatedFundManager->setId(1);
    $updatedFundManager->setName('Updated Manager');

    $repository = \Mockery::mock(FundManagerRepository::class);
    $repository->shouldReceive('exists')
        ->once()
        ->with(1)
        ->andReturn(true);
    $repository->shouldReceive('update')
        ->once()
        ->with($saveFundManagerDTO)
        ->andReturn($updatedFundManager);

    $useCase = new UpdateFundManagerUseCase($repository);

    $result = $useCase->execute($saveFundManagerDTO);

    expect($result)->toBe($updatedFundManager);
});

test('it propagates repository exception when updating fund manager', function () {
    $saveFundManagerDTO = new SaveFundManagerDTO('Updated Manager', 1);

    $previousException = new RuntimeException('database is unavailable');

    $repository = \Mockery::mock(FundManagerRepository::class);
    $repository->shouldReceive('exists')
        ->once()
        ->with(1)
        ->andReturn(true);
    $repository->shouldReceive('update')
        ->once()
        ->with($saveFundManagerDTO)
        ->andThrow($previousException);

    $useCase = new UpdateFundManagerUseCase($repository);

    try {
        $useCase->execute($saveFundManagerDTO);
        $this->fail('Expected RuntimeException was not thrown.');
    } catch (RuntimeException $exception) {
        expect($exception)->toBe($previousException)
            ->and($exception->getMessage())->toBe('database is unavailable');
    }
});

test('it throws not found when fund manager does not exist before updating', function () {
    $saveFundManagerDTO = new SaveFundManagerDTO('Updated Manager', 1);

    $repository = \Mockery::mock(FundManagerRepository::class);
    $repository->shouldReceive('exists')
        ->once()
        ->with(1)
        ->andReturn(false);
    $repository->shouldNotReceive('update');

    $useCase = new UpdateFundManagerUseCase($repository);

    try {
        $useCase->execute($saveFundManagerDTO);
        $this->fail('Expected RuntimeException was not thrown.');
    } catch (RuntimeException $exception) {
        expect($exception->getMessage())->toBe('Fund manager not found.')
            ->and($exception->getCode())->toBe(404);
    }
});
