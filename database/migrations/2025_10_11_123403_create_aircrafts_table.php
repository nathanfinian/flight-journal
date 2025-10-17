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
        Schema::create('aircrafts', function (Blueprint $table) {
            $table->id();
            $table->string('type_name', 40)->unique();   // e.g., ATR 72-600
            $table->string('icao_code', 4)->nullable();  // e.g., AT76
            $table->string('iata_code', 3)->nullable();  // e.g., ATR
            $table->unsignedSmallInteger('seat_capacity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aircrafts');
    }
};
