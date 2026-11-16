<?php

namespace Database\Factories;

use App\Enums\SniffRunStatus;
use App\Models\SniffRun;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SniffRun>
 */
class SniffRunFactory extends Factory
{
    public function definition(): array
    {
        $startedAt = $this->faker->dateTimeBetween('-1 month');

        return [
            'status' => SniffRunStatus::Succeeded,
            'filters_count' => $this->faker->numberBetween(1, 4),
            'saved_count' => $this->faker->numberBetween(20, 120),
            'skipped_count' => $this->faker->numberBetween(0, 5),
            'failed_count' => 0,
            'new_entries_count' => $this->faker->numberBetween(0, 5),
            'error' => null,
            'started_at' => $startedAt,
            'finished_at' => (clone $startedAt)->modify('+'.$this->faker->numberBetween(30, 300).' seconds'),
        ];
    }

    public function running(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => SniffRunStatus::Running,
                'finished_at' => null,
            ];
        });
    }

    public function failed(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => SniffRunStatus::Failed,
                'error' => 'Illuminate\Http\Client\ConnectionException: cURL error 28: Connection timed out',
            ];
        });
    }
}
