<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Equipment;
use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\User;

class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    public function definition(): array
    {
        // Generate realistic Indonesian registration (PK-XXX)
        $registration = 'PK-' . strtoupper($this->faker->unique()->lexify('???'));

        return [
            'registration' => $registration,

            // random FK associations (ensure you have seeded these tables first)
            'aircraft_id' => Aircraft::inRandomOrder()->value('id') ?? Aircraft::factory(),
            'airline_id'  => Airline::inRandomOrder()->value('id') ?? Airline::factory(),

            'status' => $this->faker->randomElement(['ACTIVE', 'RETIRED']),

            // audit fields (nullable)
            'created_by' => User::inRandomOrder()->value('id') ?? null,
            'updated_by' => User::inRandomOrder()->value('id') ?? null,

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * State for active equipment only.
     */
    public function active(): static
    {
        return $this->state(fn() => ['status' => 'ACTIVE']);
    }

    /**
     * State for retired equipment.
     */
    public function retired(): static
    {
        return $this->state(fn() => ['status' => 'RETIRED']);
    }
}
