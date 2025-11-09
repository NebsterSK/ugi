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
            'seen_at' => $this->faker->optional(0.2)->dateTime(),
            'favorited_at' => $this->faker->optional(0.2)->dateTime(),
            'is_ignored' => $this->faker->boolean(),
            // TODO
            'comment' => $this->faker->optional(0.2)->sentence(),
        ];
    }

    public function newState(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'seen_at' => null,
                'favorited_at' => null,
                'is_ignored' => false,
            ];
        });
    }

    public function seen(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'seen_at' => now(),
                'favorited_at' => null,
                'is_ignored' => false,
            ];
        });
    }

    public function ignored(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'seen_at' => null,
                'favorited_at' => null,
                'is_ignored' => true,
            ];
        });
    }

    public function favorite(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'seen_at' => now(),
                'favorited_at' => now(),
                'is_ignored' => false,
            ];
        });
    }
}
