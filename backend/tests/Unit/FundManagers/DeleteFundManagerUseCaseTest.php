<?php

use FMS\Core\Contracts\FundManagerRepository;
use FMS\Core\UseCases\DeleteFundManagerUseCase;

test('it delegates delete to fund manager repository', function () {
    $repository = \Mockery::mock(FundManagerRepository::class);
    $repository->shouldReceive('hasActiveFunds')
        ->once()
        ->with(1)
        ->andReturn(false);
    $repository->shouldReceive('delete')
        ->once()
        ->with(1)
        ->andReturn(true);

    $useCase = new DeleteFundManagerUseCase($repository);

    $useCase->execute(1);

    expect(true)->toBeTrue();
});

test('it throws runtime exception when fund manager is not found', function () {
    $repository = \Mockery::mock(FundManagerRepository::class);
    $repository->shouldReceive('hasActiveFunds')
        ->once()
        ->with(999)
        ->andReturn(false);
    $repository->shouldReceive('delete')
        ->once()
        ->with(999)
        ->andReturn(false);

    $useCase = new DeleteFundManagerUseCase($repository);

    try {
        $useCase->execute(999);
        $this->fail('Expected RuntimeException was not thrown.');
    } catch (RuntimeException $exception) {
        expect($exception->getMessage())->toBe('Fund manager not found.')
            ->and($exception->getCode())->toBe(404);
    }
});

test('it throws relation error when fund manager has active funds', function () {
    $repository = \Mockery::mock(FundManagerRepository::class);
    $repository->shouldReceive('hasActiveFunds')
        ->once()
        ->with(1)
        ->andReturn(true);
    $repository->shouldReceive('delete')
        ->never();

    $useCase = new DeleteFundManagerUseCase($repository);

    try {
        $useCase->execute(1);
        $this->fail('Expected RuntimeException was not thrown.');
    } catch (RuntimeException $exception) {
        expect($exception->getMessage())->toBe('Fund manager has active funds and cannot be deleted.')
            ->and($exception->getCode())->toBe(409);
    }
});
