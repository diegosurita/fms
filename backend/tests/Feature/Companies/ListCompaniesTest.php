<?php

use Illuminate\Support\Facades\DB;

test('it returns the registered list of companies from endpoint', function () {
    $firstCompanyId = DB::table('companies')->insertGetId([
        'name' => 'Alpha Company',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $secondCompanyId = DB::table('companies')->insertGetId([
        'name' => 'Beta Company',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->get('/v1/companies');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
    $response->assertJsonFragment([
        'id' => $firstCompanyId,
        'name' => 'Alpha Company',
    ]);
    $response->assertJsonFragment([
        'id' => $secondCompanyId,
        'name' => 'Beta Company',
    ]);
});

test('it filters companies by name from endpoint', function () {
    $targetCompanyId = DB::table('companies')->insertGetId([
        'name' => 'Target Company',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('companies')->insertGetId([
        'name' => 'Other Company',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->get('/v1/companies?filter=target');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonFragment([
        'id' => $targetCompanyId,
        'name' => 'Target Company',
    ]);
    $response->assertJsonPath('data.0.id', $targetCompanyId);
    $response->assertJsonPath('data.0.name', 'Target Company');
});

test('it does not return soft deleted companies from endpoint', function () {
    $activeCompanyId = DB::table('companies')->insertGetId([
        'name' => 'Active Company',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $deletedCompanyId = DB::table('companies')->insertGetId([
        'name' => 'Deleted Company',
        'created_at' => now(),
        'updated_at' => now(),
        'deleted_at' => now(),
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->get('/v1/companies');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonFragment([
        'id' => $activeCompanyId,
        'name' => 'Active Company',
    ]);
    $returnedIds = collect($response->json('data'))->pluck('id')->all();
    expect($returnedIds)->not->toContain($deletedCompanyId);
});
