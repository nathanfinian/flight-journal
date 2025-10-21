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
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_no', 20);  // ID-6200
            $table->foreignId('airport_route_id')->constrained('airport_routes')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->date('service_date');     // 2025-07-01
            $table->foreignId('equipment_id')->constrained('equipments')
                ->cascadeOnUpdate()->restrictOnDelete();

            // Optional planned times
            $table->time('sched_dep')->nullable();
            $table->time('sched_arr')->nullable();

            // Actuals from the sheet
            $table->time('actual_arr')->nullable();   // TIBA WIB
            $table->time('actual_dep')->nullable();   // BERANGKAT WIB

            $table->string('notes', 255)->nullable();

            $table->unique(['flight_no', 'service_date'], 'uniq_flight_instance');
            $table->index(['airport_route_id', 'service_date'], 'idx_route_date');

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
        Schema::dropIfExists('flights');
    }
};
