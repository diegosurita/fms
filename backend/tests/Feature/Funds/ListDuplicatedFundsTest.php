<?php

use Illuminate\Support\Facades\DB;

test('it returns duplicated funds list from endpoint', function () {
    $managerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Manager 1',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $sourceFundId = DB::table('funds')->insertGetId([
        'name' => 'Alpha Fund',
        'start_year' => 2020,
        'manager_id' => $managerId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $duplicatedFundId = DB::table('funds')->insertGetId([
        'name' => 'Alpha Fund II',
        'start_year' => 2021,
        'manager_id' => $managerId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('duplicated_funds')->insert([
        'source_fund_id' => $sourceFundId,
        'duplicated_fund_id' => $duplicatedFundId,
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->get('/v1/funds/duplicated');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.source.id', $sourceFundId);
    $response->assertJsonPath('data.0.source.name', 'Alpha Fund');
    $response->assertJsonPath('data.0.source.start_year', 2020);
    $response->assertJsonPath('data.0.source.manager_id', $managerId);
    $response->assertJsonPath('data.0.duplicated.id', $duplicatedFundId);
    $response->assertJsonPath('data.0.duplicated.name', 'Alpha Fund II');
    $response->assertJsonPath('data.0.duplicated.start_year', 2021);
    $response->assertJsonPath('data.0.duplicated.manager_id', $managerId);
});

test('it does not return duplicated funds when one fund is soft deleted', function () {
    $managerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Manager 1',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $sourceFundId = DB::table('funds')->insertGetId([
        'name' => 'Source Fund',
        'start_year' => 2018,
        'manager_id' => $managerId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $deletedDuplicatedFundId = DB::table('funds')->insertGetId([
        'name' => 'Deleted Duplicated Fund',
        'start_year' => 2019,
        'manager_id' => $managerId,
        'created_at' => now(),
        'updated_at' => now(),
        'deleted_at' => now(),
    ]);

    DB::table('duplicated_funds')->insert([
        'source_fund_id' => $sourceFundId,
        'duplicated_fund_id' => $deletedDuplicatedFundId,
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->get('/v1/funds/duplicated');

    $response->assertOk();
    $response->assertJsonCount(0, 'data');
});
