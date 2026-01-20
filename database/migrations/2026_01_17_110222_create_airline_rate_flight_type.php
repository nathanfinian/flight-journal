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
        Schema::create('airline_rate_flight_type', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('airline_rate_id')
                ->constrained('airline_rates')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('flight_type_id')
                ->constrained('flight_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Percentage modifier
            // Example: +10%, -5%, etc
            $table->decimal('percentage', 5, 2);
            // allows -99.99% to +99.99%

            // Audit (optional but recommended)
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // 🔒 HARD RULE: only ONE of each flightType per airlineRate
            $table->unique(
                ['airline_rate_id', 'flight_type_id'],
                'uniq_airline_rate_flight_type'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airline_rate_flight_type');
    }
};
