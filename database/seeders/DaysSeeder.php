<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DaysSeeder extends Seeder
{
    public function run(): void
    {
        // 0 = Sunday .. 6 = Saturday
        $rows = [
            ['day_name' => 'Sunday',    'dow' => 0],
            ['day_name' => 'Monday',    'dow' => 1],
            ['day_name' => 'Tuesday',   'dow' => 2],
            ['day_name' => 'Wednesday', 'dow' => 3],
            ['day_name' => 'Thursday',  'dow' => 4],
            ['day_name' => 'Friday',    'dow' => 5],
            ['day_name' => 'Saturday',  'dow' => 6],
        ];

        // Idempotent: insert new or update if exists (by unique keys)
        DB::table('days')->upsert(
            $rows,
            // unique-by (must match your unique columns)
            ['dow', 'day_name'],
            // columns to update if a conflict occurs (none really need updating)
            ['day_name']
        );
    }
}
