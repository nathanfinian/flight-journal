<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Equipment;
use App\Models\AirportRoute;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);
        $this->call([
            Cabang::class,
        ]);
        
        User::create([
            'name' => 'Nathanael Finian',
            'username' => 'nathan',
            'role_id' => 1,
            // 'branch_id' => 1,
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

        Equipment::factory()->count(10)->active()->create();

        AirportRoute::factory()
            ->count(10)
            ->active()
            ->create();
    }
}
