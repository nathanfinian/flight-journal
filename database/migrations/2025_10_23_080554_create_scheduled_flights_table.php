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
        Schema::create('scheduled_flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_no', 10)->unique();  // ID-6200
            $table->foreignId('airline_route_id')->constrained('airport_routes')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('equipment_id')
                ->nullable()
                ->constrained('equipments')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('branch_id')->constrained('branches')
                ->cascadeOnUpdate()->restrictOnDelete();

            // Optional planned times
            $table->time('sched_dep')->nullable();
            $table->time('sched_arr')->nullable();

            $table->foreignId('created_by')->nullable()
                ->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('updated_by')->nullable()
                ->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_flights');
    }
};
