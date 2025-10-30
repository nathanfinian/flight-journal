<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AirportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('airports')->insert([
            [
                'iata' => 'CGK',
                'icao' => 'WIII',
                'city' => 'Jakarta',
                'country' => 'IDN',
                'tz' => 'Asia/Jakarta',
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'iata' => 'SUB',
                'icao' => 'WARR',
                'city' => 'Surabaya',
                'country' => 'IDN',
                'tz' => 'Asia/Jakarta',
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'iata' => 'DPS',
                'icao' => 'WADD',
                'city' => 'Denpasar (Bali)',
                'country' => 'IDN',
                'tz' => 'Asia/Makassar',
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'iata' => 'PNK',
                'icao' => 'WIOO',
                'city' => 'Pontianak',
                'country' => 'IDN',
                'tz' => 'Asia/Jakarta',
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'iata' => 'PKY',
                'icao' => 'WAOP',
                'city' => 'Palangkaraya',
                'country' => 'IDN',
                'tz' => 'Asia/Jakarta',
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'iata' => 'SMQ',
                'icao' => 'WRBS',
                'city' => 'Sampit',
                'country' => 'IDN',
                'tz' => 'Asia/Jakarta', // Central Kalimantan uses WIB (UTC+7)
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'iata' => 'SKJ',
                'icao' => 'WIOD',
                'city' => 'Singkawang',
                'country' => 'IDN',
                'tz' => 'Asia/Jakarta', // West Kalimantan, WIB (UTC+7)
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ]);
    }
}
