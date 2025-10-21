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
                'iata' => 'UPG',
                'icao' => 'WAAA',
                'city' => 'Makassar',
                'country' => 'IDN',
                'tz' => 'Asia/Makassar',
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'iata' => 'KNO',
                'icao' => 'WIMM',
                'city' => 'Medan (Kualanamu)',
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
                'tz' => 'Asia/Makassar',
                'created_at' => $now,
                'updated_at' => $now,
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ]);
    }
}
