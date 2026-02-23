<?php

use Illuminate\Support\Facades\DB;

test('it updates a fund through endpoint', function () {
    $originalManagerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Manager 1',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $newManagerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Manager 2',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $fundId = DB::table('funds')->insertGetId([
        'name' => 'Alpha Fund',
        'start_year' => 2020,
        'manager_id' => $originalManagerId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->putJson("/v1/funds/{$fundId}", [
        'name' => 'Updated Fund',
        'start_year' => 2024,
        'manager_id' => $newManagerId,
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.id', $fundId);
    $response->assertJsonPath('data.name', 'Updated Fund');
    $response->assertJsonPath('data.start_year', 2024);
    $response->assertJsonPath('data.manager_id', $newManagerId);

    $this->assertDatabaseHas('funds', [
        'id' => $fundId,
        'name' => 'Updated Fund',
        'start_year' => 2024,
        'manager_id' => $newManagerId,
    ]);
});

test('it validates required fields when updating fund through endpoint', function () {
    $managerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Manager 1',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $fundId = DB::table('funds')->insertGetId([
        'name' => 'Alpha Fund',
        'start_year' => 2020,
        'manager_id' => $managerId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->putJson("/v1/funds/{$fundId}", []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'name',
        'start_year',
        'manager_id',
    ]);
});

test('it updates fund aliases through endpoint', function () {
    $managerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Manager 1',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $fundId = DB::table('funds')->insertGetId([
        'name' => 'Alpha Fund',
        'start_year' => 2020,
        'manager_id' => $managerId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('fund_aliases')->insert([
        [
            'alias' => 'Old Alias 1',
            'fund' => $fundId,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'alias' => 'Old Alias 2',
            'fund' => $fundId,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->putJson("/v1/funds/{$fundId}", [
        'name' => 'Alpha Fund',
        'start_year' => 2020,
        'manager_id' => $managerId,
        'aliases' => ['New Alias 1', 'New Alias 2'],
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.aliases', ['New Alias 1', 'New Alias 2']);

    $this->assertDatabaseMissing('fund_aliases', [
        'fund' => $fundId,
        'alias' => 'Old Alias 1',
    ]);

    $this->assertDatabaseMissing('fund_aliases', [
        'fund' => $fundId,
        'alias' => 'Old Alias 2',
    ]);

    $this->assertDatabaseHas('fund_aliases', [
        'fund' => $fundId,
        'alias' => 'New Alias 1',
    ]);

    $this->assertDatabaseHas('fund_aliases', [
        'fund' => $fundId,
        'alias' => 'New Alias 2',
    ]);
});
