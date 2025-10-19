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
        Schema::create('airline_route', function (Blueprint $table) {
            $table->id();

            $table->foreignId('airline_id')
                ->constrained('airlines')->cascadeOnUpdate()->restrictOnDelete();

            $table->foreignId('route_id')
                ->constrained('routes')->cascadeOnUpdate()->restrictOnDelete();

            // Optional flags/metadata (seasonality, codeshare, etc.)
            $table->boolean('is_primary_operator')->default(false);
            $table->string('notes')->nullable();

            $table->timestamps();

            // Ensure unique pairing
            $table->unique(['airline_id', 'route_id'], 'uniq_airline_route');

            // Common filters
            $table->index(['route_id', 'airline_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airline_route');
    }
};
