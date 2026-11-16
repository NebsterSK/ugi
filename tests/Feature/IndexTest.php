<?php

use App\Models\SniffRun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists sniff runs with their errors to a signed-in user', function () {
    SniffRun::factory()->create(['started_at' => '2026-11-15 06:00:00']);
    SniffRun::factory()->failed()->create(['started_at' => '2026-11-15 12:00:00', 'error' => 'Markup changed']);

    $this->actingAs(User::factory()->create())
        ->get(route('index'))
        ->assertOk()
        ->assertSeeInOrder(['2026-11-15 12:00', 'Failed', 'Markup changed', '2026-11-15 06:00', 'Succeeded']);
});

it('shows a run that never finished as interrupted', function () {
    SniffRun::factory()->running()->create(['started_at' => now()->subHours(2)]);

    $this->actingAs(User::factory()->create())
        ->get(route('index'))
        ->assertSee('Interrupted');
});

it('hides sniff runs from guests', function () {
    SniffRun::factory()->failed()->create(['error' => 'Markup changed']);

    $this->get(route('index'))
        ->assertOk()
        ->assertDontSee('Sniff runs')
        ->assertDontSee('Markup changed');
});

it('prunes runs older than three months', function () {
    $old = SniffRun::factory()->create(['started_at' => now()->subMonths(3)->subDay()]);
    $recent = SniffRun::factory()->create(['started_at' => now()->subMonths(2)]);

    $this->artisan('model:prune', ['--model' => SniffRun::class])->assertSuccessful();

    expect(SniffRun::pluck('id')->all())->toBe([$recent->id]);
});
