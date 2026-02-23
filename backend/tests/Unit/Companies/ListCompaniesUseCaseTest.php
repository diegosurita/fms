<?php

use FMS\Core\Contracts\CompanyRepository;
use FMS\Core\Entities\CompanyEntity;
use FMS\Core\UseCases\ListCompaniesUseCase;

test('it delegates to company repository and returns listed companies', function () {
    $company = new CompanyEntity();
    $company->setId(1);
    $company->setName('Company 1');

    $expectedCompanies = [$company];

    $repository = \Mockery::mock(CompanyRepository::class);
    $repository->shouldReceive('list')
        ->once()
        ->with(null)
        ->andReturn($expectedCompanies);

    $useCase = new ListCompaniesUseCase($repository);

    $result = $useCase->execute();

    expect($result)->toBe($expectedCompanies);
});

test('it delegates filter to company repository', function () {
    $repository = \Mockery::mock(CompanyRepository::class);
    $repository->shouldReceive('list')
        ->once()
        ->with('target')
        ->andReturn([]);

    $useCase = new ListCompaniesUseCase($repository);

    $result = $useCase->execute('target');

    expect($result)->toBe([]);
});
