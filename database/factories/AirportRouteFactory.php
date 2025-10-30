<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AirportRoute;
use App\Models\Airport;
use App\Models\Airline;
use App\Models\User;

class AirportRouteFactory extends Factory
{
    protected $model = AirportRoute::class;

    public function definition(): array
    {
        // Ensure two distinct airports
        $origin = Airport::inRandomOrder()->first();
        $destination = Airport::where('id', '!=', $origin?->id)->inRandomOrder()->first();

        return [
            'origin_id'      => $origin?->id ?? Airport::factory(),
            'destination_id' => $destination?->id ?? Airport::factory(),
            'status'         => $this->faker->randomElement(['ACTIVE', 'INACTIVE']),
            'created_by'     => User::inRandomOrder()->value('id') ?? null,
            'updated_by'     => User::inRandomOrder()->value('id') ?? null,
            'created_at'     => now(),
            'updated_at'     => now(),
        ];
    }

    /**
     * Configure the factory to attach airlines automatically.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (AirportRoute $route) {
            // Get 1–3 random airlines
            $airlines = Airline::inRandomOrder()->take(rand(1, 3))->pluck('id');

            if ($airlines->isEmpty()) {
                // create at least one airline if none exist
                $airlines = collect([Airline::factory()->create()->id]);
            }

            $route->airlines()->attach($airlines);
        });
    }

    public function active(): static
    {
        return $this->state(fn() => ['status' => 'ACTIVE']);
    }

    public function inactive(): static
    {
        return $this->state(fn() => ['status' => 'INACTIVE']);
    }
}
