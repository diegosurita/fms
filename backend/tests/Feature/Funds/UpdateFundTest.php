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
        'startYear' => 2024,
        'managerId' => $newManagerId,
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.id', $fundId);
    $response->assertJsonPath('data.name', 'Updated Fund');
    $response->assertJsonPath('data.startYear', 2024);
    $response->assertJsonPath('data.managerId', $newManagerId);

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
        'startYear',
        'managerId',
    ]);
});
