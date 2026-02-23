<?php

use FMS\Core\Contracts\CompanyRepository;
use FMS\Core\DataTransferObjects\SaveCompanyDTO;
use FMS\Core\Entities\CompanyEntity;
use FMS\Core\UseCases\CreateCompanyUseCase;

test('it delegates to company repository and returns created company', function () {
    $saveCompanyDTO = new SaveCompanyDTO('Alpha Company');

    $createdCompany = new CompanyEntity();
    $createdCompany->setId(1);
    $createdCompany->setName('Alpha Company');

    $repository = \Mockery::mock(CompanyRepository::class);
    $repository->shouldReceive('create')
        ->once()
        ->with($saveCompanyDTO)
        ->andReturn($createdCompany);

    $useCase = new CreateCompanyUseCase($repository);

    $result = $useCase->execute($saveCompanyDTO);

    expect($result)->toBe($createdCompany);
});

test('it propagates repository exception when creating company', function () {
    $saveCompanyDTO = new SaveCompanyDTO('Alpha Company');

    $previousException = new RuntimeException('database is unavailable');

    $repository = \Mockery::mock(CompanyRepository::class);
    $repository->shouldReceive('create')
        ->once()
        ->with($saveCompanyDTO)
        ->andThrow($previousException);

    $useCase = new CreateCompanyUseCase($repository);

    try {
        $useCase->execute($saveCompanyDTO);
        $this->fail('Expected RuntimeException was not thrown.');
    } catch (RuntimeException $exception) {
        expect($exception)->toBe($previousException)
            ->and($exception->getMessage())->toBe('database is unavailable');
    }
});
