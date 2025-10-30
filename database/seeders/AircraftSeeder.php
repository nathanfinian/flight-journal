<?php

namespace Database\Seeders;

use App\Models\Aircraft;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AircraftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $rows = [
            // Regional turboprops
            ['type_name' => 'ATR 72-600',           'icao_code' => 'AT76', 'iata_code' => 'ATR', 'seat_capacity' => 70],

            // Narrow-bodies
            ['type_name' => 'Airbus A320-200',      'icao_code' => 'A320', 'iata_code' => 'A32', 'seat_capacity' => 180],
            ['type_name' => 'Airbus A320neo',       'icao_code' => 'A20N', 'iata_code' => 'A32', 'seat_capacity' => 186],
            ['type_name' => 'Boeing 737-800',       'icao_code' => 'B738', 'iata_code' => 'B73', 'seat_capacity' => 189],
            ['type_name' => 'Boeing 737-900ER', 'icao_code' => 'B739', 'iata_code' => '739', 'seat_capacity' => 215],
            ['type_name' => 'Boeing 737 MAX 8', 'icao_code' => 'B38M', 'iata_code' => 'M8',  'seat_capacity' => 178],
        ];

        foreach ($rows as $row) {
            Aircraft::updateOrCreate(
                ['type_name' => $row['type_name']], // unique key
                [
                    'icao_code'     => $row['icao_code'],
                    'iata_code'     => $row['iata_code'],
                    'seat_capacity' => $row['seat_capacity'],
                    'updated_at'    => $now,
                    // if your model uses timestamps and you want to set created_at when creating:
                    'created_at'    => $now,
                ]
            );
        }
    }
}
