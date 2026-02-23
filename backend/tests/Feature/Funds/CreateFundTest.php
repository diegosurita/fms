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
        'startYear',
        'managerId',
    ]);
});
