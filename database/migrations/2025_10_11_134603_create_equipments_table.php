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
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->string('registration', 10)->unique();  // PK-LAO
            $table->foreignId('aircraft_id')                // FK to aircraft type/specs
                ->constrained('aircrafts')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->enum('status', ['ACTIVE', 'MAINT', 'RETIRED'])->default('ACTIVE');
            $table->timestamps();

            $table->foreignId('created_by')->nullable()
                ->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('updated_by')->nullable()
                ->constrained('users')->nullOnDelete()->cascadeOnUpdate();

            $table->index(['aircraft_id', 'status'], 'idx_equipment_aircraft_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
