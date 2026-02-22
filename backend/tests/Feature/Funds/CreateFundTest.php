<?php

use Illuminate\Support\Facades\DB;

test('it creates a fund through endpoint', function () {
    $managerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Manager 1',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/v1/funds', [
        'name' => 'Alpha Fund',
        'startYear' => 2022,
        'managerId' => $managerId,
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.name', 'Alpha Fund');
    $response->assertJsonPath('data.startYear', 2022);
    $response->assertJsonPath('data.managerId', $managerId);
    $response->assertJsonPath('data.id', fn (mixed $id): bool => is_int($id) && $id > 0);

    $this->assertDatabaseHas('funds', [
        'name' => 'Alpha Fund',
        'start_year' => 2022,
        'manager_id' => $managerId,
    ]);
});

test('it validates required fields when creating fund through endpoint', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/v1/funds', []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'name',
        'startYear',
        'managerId',
    ]);
});
