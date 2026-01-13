<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FlightTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name'       => 'Regular',
                'type_code'  => 'REG',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Charter',
                'type_code'  => 'CHR',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Extra Flight',
                'type_code'  => 'EXT',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Cargo',
                'type_code'  => 'CRG',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Technical Stop',
                'type_code'  => 'TEC',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('flight_types')->insert($data);
    }
}
