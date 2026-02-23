<?php

use Illuminate\Support\Facades\DB;

test('it updates a fund manager through endpoint', function () {
    $fundManagerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Alpha Manager',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->putJson("/v1/fund-managers/{$fundManagerId}", [
        'name' => 'Updated Manager',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.id', $fundManagerId);
    $response->assertJsonPath('data.name', 'Updated Manager');

    $this->assertDatabaseHas('fund_managers', [
        'id' => $fundManagerId,
        'name' => 'Updated Manager',
    ]);
});

test('it validates required fields when updating fund manager through endpoint', function () {
    $fundManagerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Alpha Manager',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->putJson("/v1/fund-managers/{$fundManagerId}", []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'name',
    ]);
});
