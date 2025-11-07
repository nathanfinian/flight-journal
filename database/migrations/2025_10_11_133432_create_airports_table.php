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
        Schema::create('airports', function (Blueprint $table) {
            $table->id();
            $table->char('iata', 3)->unique();         // CGK, PKY
            $table->char('icao', 4)->unique()->nullable();     // WIII, WAOO, etc.
            $table->string('city', 120)->nullable();
            $table->string('country', 80)->nullable();
            $table->string('tz', 40)->default('Asia/Jakarta'); // IANA tz
            $table->timestamps();

            $table->foreignId('created_by')->nullable()
                ->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('updated_by')->nullable()
                ->constrained('users')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airports');
    }
};
