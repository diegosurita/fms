<?php

use Illuminate\Support\Facades\DB;

test('it deletes a fund through endpoint', function () {
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
    $response = $this->deleteJson("/v1/funds/{$fundId}");

    $response->assertNoContent();

    $this->assertDatabaseMissing('funds', [
        'id' => $fundId,
    ]);
});
