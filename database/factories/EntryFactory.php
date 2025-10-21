<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entry>
 */
class EntryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'url' => $this->faker->url(),
            'title' => $this->faker->sentence(),
            'slug' => $this->faker->slug(),
        ];
    }
}
