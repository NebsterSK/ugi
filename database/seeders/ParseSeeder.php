<?php

namespace Database\Seeders;

use App\Models\Parse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParseSeeder extends Seeder
{
    public function run(): void
    {
        Parse::factory()->count(10)->create();
    }
}
