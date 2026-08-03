<?php

use App\Models\Entry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('requires authentication', function () {
    $this->get(route('statistics.index'))->assertRedirect(route('login'));
});

it('shows how many apartments were added per month, split by state', function () {
    Entry::factory()->count(2)->newState()->create(['created_at' => '2026-01-15 10:00:00']);
    Entry::factory()->seen()->create(['created_at' => '2026-01-20 10:00:00']);
    Entry::factory()->favorite()->create(['created_at' => '2026-03-05 10:00:00']);
    Entry::factory()->ignored()->create(['created_at' => '2026-03-06 10:00:00']);

    $this->actingAs(User::factory()->create())
        ->get(route('statistics.index'))
        ->assertOk()
        ->assertSee('2026-01')
        ->assertSee('2026-02')
        ->assertSee('2026-03')
        ->assertViewHas('entriesPerMonth', fn ($months) => $months->all() === [
            '2026-01' => ['ignored' => 0, 'seen' => 1, 'favorite' => 0, 'new' => 2, 'total' => 3],
            '2026-02' => ['ignored' => 0, 'seen' => 0, 'favorite' => 0, 'new' => 0, 'total' => 0],
            '2026-03' => ['ignored' => 1, 'seen' => 0, 'favorite' => 1, 'new' => 0, 'total' => 2],
        ])
        ->assertViewHas('total', 5)
        ->assertViewHas('best', 3);
});

it('shows the price and price per m2 distribution', function () {
    Entry::factory()->create(['price' => 245000, 'price_per_sqm' => 2050]);
    Entry::factory()->create(['price' => 251000, 'price_per_sqm' => 2150]);
    Entry::factory()->create(['price' => 285000, 'price_per_sqm' => 2450]);

    $this->actingAs(User::factory()->create())
        ->get(route('statistics.index'))
        ->assertOk()
        ->assertViewHas('priceHistogram', fn ($buckets) => $buckets->all() === [
            ['from' => 240000, 'to' => 260000, 'count' => 2],
            ['from' => 260000, 'to' => 280000, 'count' => 0],
            ['from' => 280000, 'to' => 300000, 'count' => 1],
        ])
        ->assertViewHas('pricePerSqmHistogram', fn ($buckets) => $buckets->all() === [
            ['from' => 2000, 'to' => 2200, 'count' => 2],
            ['from' => 2200, 'to' => 2400, 'count' => 0],
            ['from' => 2400, 'to' => 2600, 'count' => 1],
        ]);
});

it('shows the median price per m2 per month', function () {
    Entry::factory()->create(['created_at' => '2026-01-10 10:00:00', 'price_per_sqm' => 2000]);
    Entry::factory()->create(['created_at' => '2026-01-11 10:00:00', 'price_per_sqm' => 2400]);
    Entry::factory()->create(['created_at' => '2026-03-11 10:00:00', 'price_per_sqm' => 3000]);

    $this->actingAs(User::factory()->create())
        ->get(route('statistics.index'))
        ->assertOk()
        ->assertViewHas('medianPricePerSqmPerMonth', fn ($months) => $months->all() === [
            '2026-01' => 2200,
            '2026-02' => null,
            '2026-03' => 3000,
        ]);
});

it('shows counts and medians per district', function () {
    Entry::factory()->count(2)->create(['district' => 'Rača', 'price' => 250000, 'price_per_sqm' => 2500, 'area' => 100]);
    Entry::factory()->create(['district' => 'Ružinov', 'price' => 300000, 'price_per_sqm' => 3000, 'area' => 100]);

    $this->actingAs(User::factory()->create())
        ->get(route('statistics.index'))
        ->assertOk()
        ->assertSee('Rača')
        ->assertViewHas('districts', fn ($districts) => $districts->all() === [
            ['district' => 'Rača', 'count' => 2, 'price' => 250000, 'price_per_sqm' => 2500, 'area' => 100],
            ['district' => 'Ružinov', 'count' => 1, 'price' => 300000, 'price_per_sqm' => 3000, 'area' => 100],
        ]);
});

it('handles having no apartments at all', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('statistics.index'))
        ->assertOk()
        ->assertSee('No apartments yet.')
        ->assertViewHas('total', 0);
});
