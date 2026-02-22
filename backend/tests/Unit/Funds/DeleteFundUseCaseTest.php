<?php

use FMS\Core\Contracts\FundRepository;
use FMS\Core\UseCases\DeleteFundUseCase;

test('it delegates delete to fund repository', function () {
    $repository = \Mockery::mock(FundRepository::class);
    $repository->shouldReceive('delete')
        ->once()
        ->with(1)
        ->andReturn(true);

    $useCase = new DeleteFundUseCase($repository);

    $useCase->execute(1);

    expect(true)->toBeTrue();
});

test('it throws runtime exception when fund is not found', function () {
    $repository = \Mockery::mock(FundRepository::class);
    $repository->shouldReceive('delete')
        ->once()
        ->with(999)
        ->andReturn(false);

    $useCase = new DeleteFundUseCase($repository);

    try {
        $useCase->execute(999);
        $this->fail('Expected RuntimeException was not thrown.');
    } catch (RuntimeException $exception) {
        expect($exception->getMessage())->toBe('Fund not found.')
            ->and($exception->getCode())->toBe(404);
    }
});
