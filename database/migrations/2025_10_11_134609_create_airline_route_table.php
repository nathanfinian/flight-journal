<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('airline_routes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('airline_id')
                ->constrained('airlines')->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreignId('airport_route_id')
                ->constrained('airport_routes')->cascadeOnUpdate()->cascadeOnDelete();

            $table->string('notes')->nullable();

            $table->timestamps();

            // Ensure unique pairing
            $table->unique(['airline_id', 'airport_route_id'], 'uniq_airline_route');

            // Common filters
            $table->index(['airport_route_id', 'airline_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airline_routes');
    }
};
