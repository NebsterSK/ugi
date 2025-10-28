<?php

namespace Database\Seeders;

use App\Models\Entry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EntrySeeder extends Seeder
{
    public function run(): void
    {
        Entry::factory()->ignored()->count(5)->create();
        Entry::factory()->seen()->count(20)->create();
        Entry::factory()->favorite()->count(5)->create();
        Entry::factory()->newState()->count(10)->create();
    }
}
