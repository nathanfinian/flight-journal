<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class Cabang extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        DB::table('branches')->insert([
            [
                'name' => 'Palangkaraya',
                'airport_id' => '5',
                'status' => 'ACTIVE',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Singkawang',
                'airport_id' => '7',
                'status' => 'ACTIVE',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Pontianak',
                'airport_id' => '4',
                'status' => 'ACTIVE',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Sampit',
                'airport_id' => '6',
                'status' => 'ACTIVE',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}
