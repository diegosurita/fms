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
    $repository->shouldReceive('exists')
        ->once()
        ->with(1)
        ->andReturn(true);
    $repository->shouldReceive('update')
        ->once()
        ->with($saveCompanyDTO)
        ->andReturn($updatedCompany);

    $useCase = new UpdateCompanyUseCase($repository);

    $result = $useCase->execute($saveCompanyDTO);

    expect($result)->toBe($updatedCompany);
});

test('it propagates repository exception when updating company', function () {
    $saveCompanyDTO = new SaveCompanyDTO('Updated Company', 1);

    $previousException = new RuntimeException('database is unavailable');

    $repository = \Mockery::mock(CompanyRepository::class);
    $repository->shouldReceive('exists')
        ->once()
        ->with(1)
        ->andReturn(true);
    $repository->shouldReceive('update')
        ->once()
        ->with($saveCompanyDTO)
        ->andThrow($previousException);

    $useCase = new UpdateCompanyUseCase($repository);

    try {
        $useCase->execute($saveCompanyDTO);
        $this->fail('Expected RuntimeException was not thrown.');
    } catch (RuntimeException $exception) {
        expect($exception)->toBe($previousException)
            ->and($exception->getMessage())->toBe('database is unavailable');
    }
});

test('it throws not found when company does not exist before updating', function () {
    $saveCompanyDTO = new SaveCompanyDTO('Updated Company', 1);

    $repository = \Mockery::mock(CompanyRepository::class);
    $repository->shouldReceive('exists')
        ->once()
        ->with(1)
        ->andReturn(false);
    $repository->shouldNotReceive('update');

    $useCase = new UpdateCompanyUseCase($repository);

    try {
        $useCase->execute($saveCompanyDTO);
        $this->fail('Expected RuntimeException was not thrown.');
    } catch (RuntimeException $exception) {
        expect($exception->getMessage())->toBe('Company not found.')
            ->and($exception->getCode())->toBe(404);
    }
});
