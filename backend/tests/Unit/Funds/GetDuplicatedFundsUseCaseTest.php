<?php

use FMS\Core\Contracts\FundRepository;
use FMS\Core\DataTransferObjects\DuplicatedFundsDTO;
use FMS\Core\Entities\FundEntity;
use FMS\Core\UseCases\GetDuplicatedFundsUseCase;

test('it delegates to fund repository and returns duplicated funds', function () {
    $source = new FundEntity();
    $source->setId(1);
    $source->setName('Source Fund');
    $source->setStartYear(2020);
    $source->setManagerId(10);

    $duplicated = new FundEntity();
    $duplicated->setId(2);
    $duplicated->setName('Duplicated Fund');
    $duplicated->setStartYear(2021);
    $duplicated->setManagerId(10);

    $duplicatedFundsDTO = new DuplicatedFundsDTO($source, $duplicated);
    $expected = [$duplicatedFundsDTO];

    $repository = \Mockery::mock(FundRepository::class);
    $repository->shouldReceive('getDuplicated')
        ->once()
        ->withNoArgs()
        ->andReturn($expected);

    $useCase = new GetDuplicatedFundsUseCase($repository);

    $result = $useCase->execute();

    expect($result)->toBe($expected);
});

test('it propagates repository exceptions when listing duplicated funds', function () {
    $repository = \Mockery::mock(FundRepository::class);
    $repository->shouldReceive('getDuplicated')
        ->once()
        ->withNoArgs()
        ->andThrow(new \Exception('db error'));

    $useCase = new GetDuplicatedFundsUseCase($repository);

    try {
        $useCase->execute();

        $this->fail('Exception was not thrown.');
    } catch (\Exception $exception) {
        expect($exception->getMessage())->toBe('db error');
    }
});
