<?php

test('it creates a company through endpoint', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/v1/companies', [
        'name' => 'Alpha Company',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.name', 'Alpha Company');
    $response->assertJsonPath('data.id', fn (mixed $id): bool => is_int($id) && $id > 0);

    $this->assertDatabaseHas('companies', [
        'name' => 'Alpha Company',
    ]);
});

test('it validates required fields when creating company through endpoint', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/v1/companies', []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'name',
    ]);
});
