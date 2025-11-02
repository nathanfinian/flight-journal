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
        // Fetch all airport IDs once
        $airportIds = Airport::pluck('id')->toArray();

        // Return early if not enough airports
        if (count($airportIds) < 2) {
            throw new \Exception('Need at least 2 airports to create routes.');
        }

        // Keep generating unique pairs
        do {
            $originId = $this->faker->randomElement($airportIds);
            $destinationId = $this->faker->randomElement(array_diff($airportIds, [$originId]));
        } while (
            AirportRoute::where('origin_id', $originId)
            ->where('destination_id', $destinationId)
            ->exists()
        );

        return [
            'origin_id'      => $originId,
            'destination_id' => $destinationId,
            'status'         => $this->faker->randomElement(['ACTIVE', 'INACTIVE']),
            'created_by'     => User::inRandomOrder()->value('id') ?? null,
            'updated_by'     => User::inRandomOrder()->value('id') ?? null,
            'created_at'     => now(),
            'updated_at'     => now(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (AirportRoute $route) {
            $airlines = Airline::inRandomOrder()->take(rand(1, 3))->pluck('id');

            if ($airlines->isEmpty()) {
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
