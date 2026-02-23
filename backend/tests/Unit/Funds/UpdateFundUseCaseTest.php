<?php

use FMS\Core\Contracts\FundRepository;
use FMS\Core\DataTransferObjects\SaveFundDTO;
use FMS\Core\Entities\FundEntity;
use FMS\Core\UseCases\UpdateFundUseCase;

test('it delegates to fund repository and returns updated fund', function () {
    $saveFundDTO = new SaveFundDTO('Updated Fund', 2024, 20, 1);

    $updatedFund = new FundEntity();
    $updatedFund->setId(1);
    $updatedFund->setName('Updated Fund');
    $updatedFund->setStartYear(2024);
    $updatedFund->setManagerId(20);

    $repository = \Mockery::mock(FundRepository::class);
    $repository->shouldReceive('update')
        ->once()
        ->with($saveFundDTO)
        ->andReturn($updatedFund);

    $useCase = new UpdateFundUseCase($repository);

    $result = $useCase->execute($saveFundDTO);

    expect($result)->toBe($updatedFund);
});

test('it propagates repository exception when updating fund', function () {
    $saveFundDTO = new SaveFundDTO('Updated Fund', 2024, 20, 1);

    $previousException = new RuntimeException('database is unavailable');

    $repository = \Mockery::mock(FundRepository::class);
    $repository->shouldReceive('update')
        ->once()
        ->with($saveFundDTO)
        ->andThrow($previousException);

    $useCase = new UpdateFundUseCase($repository);

    try {
        $useCase->execute($saveFundDTO);
        $this->fail('Expected RuntimeException was not thrown.');
    } catch (RuntimeException $exception) {
        expect($exception)->toBe($previousException)
            ->and($exception->getMessage())->toBe('database is unavailable');
    }
});
