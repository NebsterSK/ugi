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
            'internal_id' => $this->faker->md5(),
            'url' => $this->faker->url(),
            'title' => $this->faker->sentence(),
            'is_seen' => $this->faker->boolean(),
            'is_favorite' => $this->faker->boolean(),
            'is_ignored' => $this->faker->boolean(),
            'comment' => $this->faker->optional(0.2)->sentence(),
        ];
    }

    public function newState(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_seen' => false,
                'is_favorite' => false,
                'is_ignored' => false,
            ];
        });
    }

    public function seen(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_seen' => true,
                'is_favorite' => false,
                'is_ignored' => false,
            ];
        });
    }

    public function ignored(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_seen' => true,
                'is_favorite' => false,
                'is_ignored' => true,
            ];
        });
    }

    public function favorite(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_seen' => true,
                'is_favorite' => true,
                'is_ignored' => false,
            ];
        });
    }
}
