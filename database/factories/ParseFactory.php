<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Parse>
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
