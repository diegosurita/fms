<?php

use Illuminate\Support\Facades\DB;

test('it updates a company through endpoint', function () {
    $companyId = DB::table('companies')->insertGetId([
        'name' => 'Alpha Company',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->putJson("/v1/companies/{$companyId}", [
        'name' => 'Updated Company',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.id', $companyId);
    $response->assertJsonPath('data.name', 'Updated Company');

    $this->assertDatabaseHas('companies', [
        'id' => $companyId,
        'name' => 'Updated Company',
    ]);
});

test('it validates required fields when updating company through endpoint', function () {
    $companyId = DB::table('companies')->insertGetId([
        'name' => 'Alpha Company',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->putJson("/v1/companies/{$companyId}", []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'name',
    ]);
});
