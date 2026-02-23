<?php

use FMS\Core\Contracts\CompanyRepository;
use FMS\Core\DataTransferObjects\SaveCompanyDTO;
use FMS\Core\Entities\CompanyEntity;
use FMS\Core\UseCases\UpdateCompanyUseCase;

test('it delegates to company repository and returns updated company', function () {
    $saveCompanyDTO = new SaveCompanyDTO('Updated Company', 1);

    $updatedCompany = new CompanyEntity();
    $updatedCompany->setId(1);
    $updatedCompany->setName('Updated Company');

    $repository = \Mockery::mock(CompanyRepository::class);
    $repository->shouldReceive('update')
        ->once()
        ->with($saveCompanyDTO)
        ->andReturn($updatedCompany);

    $useCase = new UpdateCompanyUseCase($repository);

    $result = $useCase->execute($saveCompanyDTO);

    expect($result)->toBe($updatedCompany);
});

test('it wraps repository exception when updating company', function () {
    $saveCompanyDTO = new SaveCompanyDTO('Updated Company', 1);

    $previousException = new RuntimeException('database is unavailable');

    $repository = \Mockery::mock(CompanyRepository::class);
    $repository->shouldReceive('update')
        ->once()
        ->with($saveCompanyDTO)
        ->andThrow($previousException);

    $useCase = new UpdateCompanyUseCase($repository);

    try {
        $useCase->execute($saveCompanyDTO);
        $this->fail('Expected RuntimeException was not thrown.');
    } catch (RuntimeException $exception) {
        expect($exception->getMessage())->toBe('Failed to update company.')
            ->and($exception->getCode())->toBe(0)
            ->and($exception->getPrevious())->toBe($previousException);
    }
});
