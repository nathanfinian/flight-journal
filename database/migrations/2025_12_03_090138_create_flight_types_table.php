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
        Schema::create('flight_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);       // Example: Regular, Charter, Extra Flight
            $table->string('type_code', 10);  // Example: REG, CHR, EXT

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_types');
    }
};
