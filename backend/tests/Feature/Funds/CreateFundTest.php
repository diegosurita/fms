<?php

use FMS\Core\Events\FundCreatedEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

test('it creates a fund through endpoint', function () {
    Event::fake();

    $managerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Manager 1',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/v1/funds', [
        'name' => 'Alpha Fund',
        'start_year' => 2022,
        'manager_id' => $managerId,
        'aliases' => ['Alpha I', 'Alpha II'],
    ]);

    $response->assertOk();
    $response->assertJsonPath('data.name', 'Alpha Fund');
    $response->assertJsonPath('data.start_year', 2022);
    $response->assertJsonPath('data.manager_id', $managerId);
    $response->assertJsonPath('data.aliases', ['Alpha I', 'Alpha II']);
    $response->assertJsonPath('data.id', fn (mixed $id): bool => is_int($id) && $id > 0);

    $fundId = (int) $response->json('data.id');

    $this->assertDatabaseHas('funds', [
        'name' => 'Alpha Fund',
        'start_year' => 2022,
        'manager_id' => $managerId,
    ]);

    $this->assertDatabaseHas('fund_aliases', [
        'fund' => $fundId,
        'alias' => 'Alpha I',
    ]);

    $this->assertDatabaseHas('fund_aliases', [
        'fund' => $fundId,
        'alias' => 'Alpha II',
    ]);

    Event::assertDispatched(FundCreatedEvent::class, function (FundCreatedEvent $event) use ($managerId): bool {
        return $event->fund->getName() === 'Alpha Fund'
            && $event->fund->getStartYear() === 2022
            && $event->fund->getManagerId() === $managerId
            && $event->fund->getId() !== null;
    });
});

test('it validates required fields when creating fund through endpoint', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/v1/funds', []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'name',
        'start_year',
        'manager_id',
    ]);
});

test('it validates aliases field format when creating fund through endpoint', function () {
    $managerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Manager 1',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/v1/funds', [
        'name' => 'Alpha Fund',
        'start_year' => 2022,
        'manager_id' => $managerId,
        'aliases' => ['Valid Alias', 100],
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors([
        'aliases.1',
    ]);
});

test('it throws an exception when creating a fund with an existing alias', function () {
    $managerId = DB::table('fund_managers')->insertGetId([
        'name' => 'Manager 1',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $fundId = DB::table('funds')->insertGetId([
        'name' => 'Existing Fund',
        'start_year' => 2021,
        'manager_id' => $managerId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('fund_aliases')->insert([
        'alias' => 'Existing Alias',
        'fund' => $fundId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/v1/funds', [
        'name' => 'Alpha Fund',
        'start_year' => 2022,
        'manager_id' => $managerId,
        'aliases' => ['Existing Alias'],
    ]);

    $response->assertStatus(500);
});
