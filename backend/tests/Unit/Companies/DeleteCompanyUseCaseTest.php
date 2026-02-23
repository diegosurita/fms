<?php

use FMS\Core\Contracts\CompanyRepository;
use FMS\Core\UseCases\DeleteCompanyUseCase;

test('it delegates delete to company repository', function () {
    $repository = \Mockery::mock(CompanyRepository::class);
    $repository->shouldReceive('delete')
        ->once()
        ->with(1)
        ->andReturn(true);

    $useCase = new DeleteCompanyUseCase($repository);

    $useCase->execute(1);

    expect(true)->toBeTrue();
});

test('it throws runtime exception when company is not found', function () {
    $repository = \Mockery::mock(CompanyRepository::class);
    $repository->shouldReceive('delete')
        ->once()
        ->with(999)
        ->andReturn(false);

    $useCase = new DeleteCompanyUseCase($repository);

    try {
        $useCase->execute(999);
        $this->fail('Expected RuntimeException was not thrown.');
    } catch (RuntimeException $exception) {
        expect($exception->getMessage())->toBe('Company not found.')
            ->and($exception->getCode())->toBe(404);
    }
});