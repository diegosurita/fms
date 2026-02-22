<?php

use Illuminate\Support\Facades\DB;

test('it returns the registered list of funds from endpoint', function () {
    $companyId = DB::table('companies')->insertGetId([
        'name' => 'Company 1',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $managerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Manager 1',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $firstFundId = DB::table('funds')->insertGetId([
        'name' => 'Alpha Fund',
        'start_year' => 2020,
        'manager_id' => $managerId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $secondFundId = DB::table('funds')->insertGetId([
        'name' => 'Beta Fund',
        'start_year' => 2019,
        'manager_id' => $managerId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('companies_funds')->insert([
        [
            'company' => $companyId,
            'fund' => $firstFundId,
        ],
        [
            'company' => $companyId,
            'fund' => $secondFundId,
        ],
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->get('/v1/funds');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
    $response->assertJsonFragment([
        'id' => $firstFundId,
        'name' => 'Alpha Fund',
        'startYear' => 2020,
        'managerId' => $managerId,
    ]);
    $response->assertJsonFragment([
        'id' => $secondFundId,
        'name' => 'Beta Fund',
        'startYear' => 2019,
        'managerId' => $managerId,
    ]);
});

test('it filters funds by company name from endpoint', function () {
    $targetCompanyId = DB::table('companies')->insertGetId([
        'name' => 'Target Company',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $otherCompanyId = DB::table('companies')->insertGetId([
        'name' => 'Other Company',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $managerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Manager 1',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $targetFundId = DB::table('funds')->insertGetId([
        'name' => 'Target Fund',
        'start_year' => 2020,
        'manager_id' => $managerId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $otherFundId = DB::table('funds')->insertGetId([
        'name' => 'Other Fund',
        'start_year' => 2019,
        'manager_id' => $managerId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('companies_funds')->insert([
        [
            'company' => $targetCompanyId,
            'fund' => $targetFundId,
        ],
        [
            'company' => $otherCompanyId,
            'fund' => $otherFundId,
        ],
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->get('/v1/funds?filter=target company');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonFragment([
        'id' => $targetFundId,
        'name' => 'Target Fund',
        'startYear' => 2020,
        'managerId' => $managerId,
    ]);
    $response->assertJsonPath('data.0.id', $targetFundId);
    $response->assertJsonPath('data.0.name', 'Target Fund');
});

test('it does not duplicate funds when linked to multiple companies', function () {
    $firstCompanyId = DB::table('companies')->insertGetId([
        'name' => 'Company A',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $secondCompanyId = DB::table('companies')->insertGetId([
        'name' => 'Company B',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $managerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Manager 1',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $fundId = DB::table('funds')->insertGetId([
        'name' => 'Single Fund',
        'start_year' => 2021,
        'manager_id' => $managerId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('companies_funds')->insert([
        [
            'company' => $firstCompanyId,
            'fund' => $fundId,
        ],
        [
            'company' => $secondCompanyId,
            'fund' => $fundId,
        ],
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->get('/v1/funds');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonFragment([
        'id' => $fundId,
        'name' => 'Single Fund',
        'startYear' => 2021,
        'managerId' => $managerId,
    ]);
});
