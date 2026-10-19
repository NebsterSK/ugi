<?php

use App\Livewire\NewEntryTable;
use App\Models\Entry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('colours the price per m2 background from green for the cheapest to red for the most expensive entry', function () {
    $cheapest = Entry::factory()->newState()->create(['price_per_sqm' => 2000]);
    $middle = Entry::factory()->newState()->create(['price_per_sqm' => 3000]);
    $mostExpensive = Entry::factory()->newState()->create(['price_per_sqm' => 4000]);
    Entry::factory()->ignored()->create(['price_per_sqm' => 9000]);

    expect($cheapest->price_per_sqm_background)->toBe('hsl(120, 70%, 82%)')
        ->and($middle->price_per_sqm_background)->toBe('hsl(60, 70%, 82%)')
        ->and($mostExpensive->price_per_sqm_background)->toBe('hsl(0, 70%, 82%)');
});

it('marks only the cheapest and the most expensive price per m2 as extreme', function () {
    $cheapest = Entry::factory()->newState()->create(['price_per_sqm' => 2000]);
    $middle = Entry::factory()->newState()->create(['price_per_sqm' => 3000]);
    $mostExpensive = Entry::factory()->newState()->create(['price_per_sqm' => 4000]);
    $ignored = Entry::factory()->ignored()->create(['price_per_sqm' => 9000]);

    expect($cheapest->is_price_per_sqm_extreme)->toBeTrue()
        ->and($middle->is_price_per_sqm_extreme)->toBeFalse()
        ->and($mostExpensive->is_price_per_sqm_extreme)->toBeTrue()
        ->and($ignored->is_price_per_sqm_extreme)->toBeFalse();
});

it('colours a single entry green', function () {
    expect(Entry::factory()->newState()->create()->price_per_sqm_background)->toBe('hsl(120, 70%, 82%)');
});

it('shows the price per m2 with a coloured background in the entries table and on the entry page', function () {
    $this->actingAs(User::factory()->create());

    $entry = Entry::factory()->newState()->create(['price_per_sqm' => 3232]);

    Livewire::test(NewEntryTable::class)
        ->assertSeeHtml('style="--bs-table-bg: hsl(120, 70%, 82%);"')
        ->assertSeeHtml('<span class="fw-bold">3 232</span>');

    $this->get(route('entries.show', $entry))->assertSee('<span class="px-1 rounded fw-bold" style="background-color: hsl(120, 70%, 82%)">', false);
});
