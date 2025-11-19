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
        Schema::create('actual_flights', function (Blueprint $table) {
            $table->id();
            $table->string('origin_flight_no', 10);  // ID-6100
            $table->string('departure_flight_no', 10);  // ID-6101
            
            $table->foreignId('origin_route_id')
                ->constrained('airline_routes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('departure_route_id')
                ->constrained('airline_routes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('origin_equipment_id')
                ->constrained('equipments')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('departure_equipment_id')
                ->constrained('equipments')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('service_date');     // 2025-07-01

            // Optional planned times
            $table->time('sched_dep');
            $table->time('sched_arr');

            // Actuals from the sheet
            $table->time('actual_arr');   // TIBA WIB
            $table->time('actual_dep');   // BERANGKAT WIB

            //Additional Nullable data
            $table->unsignedSmallInteger('pax')->nullable(); // adjust placement
            $table->unsignedInteger('ground_time')->nullable(); // minutes
            $table->string('pic')->nullable(); // pilot in command name/code

            $table->string('notes', 255)->nullable();

            //Origin and departure route with its date has to be unique
            $table->unique(['origin_flight_no', 'service_date'], 'uniq_origin_flight_instance');
            $table->unique(['departure_flight_no', 'service_date'], 'uniq_departure_flight_instance');

            $table->index(['origin_route_id', 'service_date'], 'idx_origin_route_date');
            $table->index(['departure_route_id', 'service_date'], 'idx_departure_route_date');


            $table->timestamps();

            $table->foreignId('created_by')->nullable()
                ->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('updated_by')->nullable()
                ->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            
            //SoftDelete
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actual_flights');
    }
};
