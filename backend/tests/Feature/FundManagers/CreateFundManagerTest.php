<?php

test('it creates a fund manager through endpoint', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/v1/fund-managers', [
        'name' => 'Alpha Manager',
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.name', 'Alpha Manager');
    $response->assertJsonPath('data.id', fn (mixed $id): bool => is_int($id) && $id > 0);

    $this->assertDatabaseHas('fund_managers', [
        'name' => 'Alpha Manager',
    ]);
});

test('it validates required fields when creating fund manager through endpoint', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/v1/fund-managers', []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'name',
    ]);
});
