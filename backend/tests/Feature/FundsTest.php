<?php

use Illuminate\Support\Facades\DB;

test('it returns the registered list of funds from endpoint', function () {
    $managerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Manager 1',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $firstFundId = DB::table('funds')->insertGetId([
        'name' => 'Alpha Fund',
        'start_year' => '2020-01-01 00:00:00',
        'manager' => $managerId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $secondFundId = DB::table('funds')->insertGetId([
        'name' => 'Beta Fund',
        'start_year' => '2019-01-01 00:00:00',
        'manager' => $managerId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->get('/v1/funds');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
    $response->assertJsonFragment([
        'id' => $firstFundId,
        'name' => 'Alpha Fund',
        'startYear' => 2020,
        'manager' => $managerId,
    ]);
    $response->assertJsonFragment([
        'id' => $secondFundId,
        'name' => 'Beta Fund',
        'startYear' => 2019,
        'manager' => $managerId,
    ]);
});
