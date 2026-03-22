<?php

namespace Database\Factories;

use App\Models\Parse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Parse>
 */
class ParseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'content' => $this->faker->randomHtml(),
        ];
    }
}
