<?php

namespace Database\Factories;

use App\Enums\Location;
use App\Enums\PropertyType;
use App\Models\Filter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Filter>
 */
class FilterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'property_type' => $this->faker->randomElement(PropertyType::cases()),
            'locations' => $this->faker->randomElements(Location::cases(), $this->faker->numberBetween(1, count(Location::cases()))),
            'price_from' => $this->faker->optional()->numberBetween(200000, 250000),
            'price_to' => $this->faker->optional()->numberBetween(250000, 300000),
            'area_from' => $this->faker->optional()->numberBetween(60, 90),
            'is_active' => true,
        ];
    }

    public function inactive(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
}
