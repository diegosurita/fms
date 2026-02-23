<?php

use Illuminate\Support\Facades\DB;

test('it returns the registered list of fund managers from endpoint', function () {
    $firstManagerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Alpha Manager',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $secondManagerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Beta Manager',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->get('/v1/fund-managers');

    $response->assertOk();
    $response->assertJsonCount(2, 'data');
    $response->assertJsonFragment([
        'id' => $firstManagerId,
        'name' => 'Alpha Manager',
    ]);
    $response->assertJsonFragment([
        'id' => $secondManagerId,
        'name' => 'Beta Manager',
    ]);
});

test('it does not return soft deleted fund managers from endpoint', function () {
    $activeManagerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Active Manager',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $deletedManagerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Deleted Manager',
        'created_at' => now(),
        'updated_at' => now(),
        'deleted_at' => now(),
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->get('/v1/fund-managers');

    $response->assertOk();
    $response->assertJsonCount(1, 'data');
    $response->assertJsonFragment([
        'id' => $activeManagerId,
        'name' => 'Active Manager',
    ]);
    $returnedIds = collect($response->json('data'))->pluck('id')->all();
    expect($returnedIds)->not->toContain($deletedManagerId);
});
