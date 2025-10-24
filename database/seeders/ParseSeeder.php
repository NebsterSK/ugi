<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParseSeeder extends Seeder
{
    public function run(): void
    {
        $sql = file_get_contents(storage_path('app/parse.sql'));

        DB::statement($sql);
    }
}
