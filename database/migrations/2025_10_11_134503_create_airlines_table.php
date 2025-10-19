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
        Schema::create('airlines', function (Blueprint $table) {
            $table->id();
            // Identity
            $table->string('name');                  // e.g., "Garuda Indonesia"
            $table->char('iata_code', 2)->nullable(); // e.g., "GA"
            $table->char('icao_code', 3)->nullable(); // e.g., "GIA"
            $table->string('callsign')->nullable();   // e.g., "GARUDA"
            $table->string('country')->nullable();

            // Optional status
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');

            // Audit
            $table->timestamps();
            $table->foreignId('created_by')->nullable()
                ->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('updated_by')->nullable()
                ->constrained('users')->nullOnDelete()->cascadeOnUpdate();

            // Indexes
            $table->unique('icao_code');
            $table->unique('iata_code');
            $table->index('name');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airlines');
    }
};
