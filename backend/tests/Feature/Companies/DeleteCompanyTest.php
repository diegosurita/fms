<?php

use Illuminate\Support\Facades\DB;

test('it deletes a company through endpoint', function () {
    $companyId = DB::table('companies')->insertGetId([
        'name' => 'Alpha Company',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->deleteJson("/v1/companies/{$companyId}");

    $response->assertNoContent();

    $this->assertSoftDeleted('companies', [
        'id' => $companyId,
    ]);
});