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
            ['day_name' => 'Senin',    'dow' => 1],
            ['day_name' => 'Selasa',   'dow' => 2],
            ['day_name' => 'Rabu', 'dow' => 3],
            ['day_name' => 'Kamis',  'dow' => 4],
            ['day_name' => 'Jumat',    'dow' => 5],
            ['day_name' => 'Sabtu',  'dow' => 6],
            ['day_name' => 'Minggu',    'dow' => 7],
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
