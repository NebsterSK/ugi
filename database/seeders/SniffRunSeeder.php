<?php

namespace Database\Seeders;

use App\Enums\SniffRunStatus;
use App\Models\SniffRun;
use Illuminate\Database\Seeder;

class SniffRunSeeder extends Seeder
{
    public function run(): void
    {
        SniffRun::factory()->count(10)->create();

        SniffRun::factory()->create([
            'status' => SniffRunStatus::Warning,
            'failed_count' => 3,
            'error' => '3 listings could not be parsed or saved.',
        ]);

        SniffRun::factory()->failed()->create();

        SniffRun::factory()->running()->create(['started_at' => now()->subHours(3)]);
    }
}
