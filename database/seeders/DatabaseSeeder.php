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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::create([
            'name' => 'Nathanael Finian',
            'email' => 'nathan@test.com',
            'password' => bcrypt('2525'),
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
