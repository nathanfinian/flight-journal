<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Nathanael Finian',
            'username' => 'nathan',
            'password' => bcrypt('2525'),
        ]);
        
        $this->call([
            DaysSeeder::class,
        ]);
        $this->call([
            AircraftSeeder::class,
        ]);
        $this->call([
            AirlineSeeder::class,
        ]);
        $this->call([
            AirportSeeder::class,
        ]);
    }
}
