<?php

use Illuminate\Support\Facades\DB;

test('it deletes a fund manager through endpoint', function () {
    $fundManagerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Alpha Manager',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->deleteJson("/v1/fund-managers/{$fundManagerId}");

    $response->assertNoContent();

    $this->assertSoftDeleted('fund_managers', [
        'id' => $fundManagerId,
    ]);
});

test('it returns error when deleting a fund manager with active funds', function () {
    $fundManagerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Alpha Manager',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('funds')->insert([
        'name' => 'Alpha Fund',
        'start_year' => 2020,
        'manager_id' => $fundManagerId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->deleteJson("/v1/fund-managers/{$fundManagerId}");

    $response->assertStatus(409);
    $response->assertJsonPath('message', 'Fund manager has active funds and cannot be deleted.');

    $this->assertDatabaseHas('fund_managers', [
        'id' => $fundManagerId,
        'deleted_at' => null,
    ]);
});
