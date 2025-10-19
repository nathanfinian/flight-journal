<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AirlineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $rows = [
            [
                'name'       => 'Garuda Indonesia',
                'iata_code'  => 'GA',
                'icao_code'  => 'GIA',
                'callsign'   => 'GARUDA',
                'country'    => 'IDN',
                'status'     => 'ACTIVE',
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Lion Air',
                'iata_code'  => 'JT',
                'icao_code'  => 'LNI',
                'callsign'   => 'LION INTER',
                'country'    => 'IDN',
                'status'     => 'ACTIVE',
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Batik Air',
                'iata_code'  => 'ID',
                'icao_code'  => 'BTK',
                'callsign'   => 'BATIK',
                'country'    => 'IDN',
                'status'     => 'ACTIVE',
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Citilink',
                'iata_code'  => 'QG',
                'icao_code'  => 'CTV',
                'callsign'   => 'CITILINK',
                'country'    => 'IDN',
                'status'     => 'ACTIVE',
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Indonesia AirAsia',
                'iata_code'  => 'QZ',
                'icao_code'  => 'AWQ',
                'callsign'   => 'WAGON AIR',
                'country'    => 'IDN',
                'status'     => 'ACTIVE',
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Malindo Airlines',
                'iata_code'  => 'OD',
                'icao_code'  => 'MXD',
                'callsign'   => 'MALINDO',
                'country'    => 'MYS',
                'status'     => 'ACTIVE',
                'created_by' => null,
                'updated_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Upsert to avoid duplicate-key issues on repeated seeding.
        DB::table('airlines')->upsert(
            $rows,
            ['icao_code'], // unique key to match on
            ['name', 'iata_code', 'callsign', 'country', 'status', 'updated_by', 'updated_at']
        );
    }
}
