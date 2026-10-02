<?php

use App\Enums\Location;
use App\Enums\PropertyType;
use App\Models\Filter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->migratedFilter = Filter::sole();

    Filter::query()->delete();
});

it('migrates the previously hardcoded search as the default filter', function () {
    expect($this->migratedFilter->is_active)->toBeTrue()
        ->and($this->migratedFilter->url())->toBe('https://www.nehnutelnosti.sk/vysledky/4-izbove-byty/predaj?locations=100012514&locations=100012524&locations=100012513&locations=100012511&priceFrom=240000&priceTo=280000&areaFrom=75');
});

it('requires authentication', function (string $method, string $route) {
    $filter = Filter::factory()->create();

    $this->call($method, route($route, $filter))->assertRedirect(route('login'));
})->with([
    ['GET', 'filters.index'],
    ['GET', 'filters.create'],
    ['POST', 'filters.store'],
    ['GET', 'filters.edit'],
    ['PUT', 'filters.update'],
    ['DELETE', 'filters.destroy'],
    ['GET', 'filters.toggleActive'],
]);

it('lists filters', function () {
    Filter::factory()->create(['name' => 'Big flats', 'locations' => [Location::Raca, Location::Ruzinov]]);

    $this->actingAs(User::factory()->create())
        ->get(route('filters.index'))
        ->assertSuccessful()
        ->assertSee('Big flats')
        ->assertSee('Rača, Ružinov');
});

it('shows the create and edit forms', function () {
    $filter = Filter::factory()->create();

    $this->actingAs(User::factory()->create());

    $this->get(route('filters.create'))->assertSuccessful()->assertSee('Petržalka');
    $this->get(route('filters.edit', $filter))->assertSuccessful()->assertSee($filter->name);
});

it('creates a filter', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('filters.store'), [
            'name' => '3-room in Petržalka',
            'property_type' => '3-izbove-byty',
            'locations' => ['100012524', '100012513'],
            'price_from' => '200000',
            'price_to' => '250000',
            'area_from' => '',
            'is_active' => '1',
        ])
        ->assertRedirect(route('filters.index'));

    $filter = Filter::sole();

    expect($filter->name)->toBe('3-room in Petržalka')
        ->and($filter->property_type)->toBe(PropertyType::ThreeRoomApartment)
        ->and($filter->locations->all())->toBe([Location::Petrzalka, Location::NoveMesto])
        ->and($filter->price_from)->toBe(200000)
        ->and($filter->price_to)->toBe(250000)
        ->and($filter->area_from)->toBeNull()
        ->and($filter->is_active)->toBeTrue();
});

it('updates a filter', function () {
    $filter = Filter::factory()->create();

    $this->actingAs(User::factory()->create())
        ->put(route('filters.update', $filter), [
            'name' => 'Renamed',
            'property_type' => '4-izbove-byty',
            'locations' => ['100012514'],
            'area_from' => '80',
            'is_active' => '0',
        ])
        ->assertRedirect(route('filters.index'));

    $filter->refresh();

    expect($filter->name)->toBe('Renamed')
        ->and($filter->property_type)->toBe(PropertyType::FourRoomApartment)
        ->and($filter->locations->all())->toBe([Location::Raca])
        ->and($filter->price_from)->toBeNull()
        ->and($filter->area_from)->toBe(80)
        ->and($filter->is_active)->toBeFalse();
});

it('validates the filter', function (array $data, string $invalidField) {
    $this->actingAs(User::factory()->create())
        ->post(route('filters.store'), [
            'name' => 'Filter',
            'property_type' => '4-izbove-byty',
            'locations' => ['100012514'],
            ...$data,
        ])
        ->assertSessionHasErrors($invalidField);

    expect(Filter::count())->toBe(0);
})->with([
    'missing name' => [['name' => ''], 'name'],
    'unknown property type' => [['property_type' => 'byty'], 'property_type'],
    'no locations' => [['locations' => []], 'locations'],
    'unknown location' => [['locations' => ['123']], 'locations.0'],
    'negative price' => [['price_from' => '-1'], 'price_from'],
    'price to below price from' => [['price_from' => '250000', 'price_to' => '200000'], 'price_to'],
    'non-numeric area' => [['area_from' => 'big'], 'area_from'],
]);

it('deletes a filter', function () {
    $filter = Filter::factory()->create();

    $this->actingAs(User::factory()->create())
        ->delete(route('filters.destroy', $filter))
        ->assertRedirect(route('filters.index'));

    expect(Filter::count())->toBe(0);
});

it('toggles a filter active and inactive', function () {
    $filter = Filter::factory()->create();

    $this->actingAs(User::factory()->create());

    $this->get(route('filters.toggleActive', $filter));
    expect($filter->refresh()->is_active)->toBeFalse();

    $this->get(route('filters.toggleActive', $filter));
    expect($filter->refresh()->is_active)->toBeTrue();
});

it('builds the search url', function () {
    $filter = Filter::factory()->make([
        'property_type' => PropertyType::ThreeRoomApartment,
        'locations' => [Location::NoveMesto, Location::Ruzinov],
        'price_from' => null,
        'price_to' => 300000,
        'area_from' => 70,
    ]);

    expect($filter->url())->toBe('https://www.nehnutelnosti.sk/vysledky/3-izbove-byty/predaj?locations=100012513&locations=100012511&priceTo=300000&areaFrom=70')
        ->and($filter->url(3))->toBe('https://www.nehnutelnosti.sk/vysledky/3-izbove-byty/predaj?locations=100012513&locations=100012511&priceTo=300000&areaFrom=70&page=3');
});
