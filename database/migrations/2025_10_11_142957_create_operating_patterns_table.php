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
        Schema::create('operating_patterns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheduled_flights_id')
                ->constrained('scheduled_flights')
                ->cascadeOnDelete();
            $table->foreignId('day_id')
                ->constrained('days')
                ->cascadeOnDelete();

            // A flight can appear at most once per day
            $table->unique(['scheduled_flights_id', 'day_id'], 'uniq_flight_day');

            // Helpful index for “all flights on a given day”
            $table->index(['day_id', 'scheduled_flights_id'], 'idx_day_flight');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operating_patterns');
    }
};
