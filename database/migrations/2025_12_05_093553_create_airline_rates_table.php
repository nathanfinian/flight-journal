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
        Schema::create('airline_rates', function (Blueprint $table) {
            $table->id();

            // Relationship with airlines
            $table->foreignId('airline_id')
                ->constrained('airlines')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Identification of rate type
            $table->string('charge_name'); // e.g. Standard Handling, International, Cargo Special
            $table->string('charge_code', 20)->unique(); // e.g. STD, INTL, CGO

            // Fees
            $table->decimal('ground_fee', 12, 2)->nullable();
            $table->decimal('cargo_fee', 12, 2)->nullable();

            // --- Audit Fields ---
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airline_rates');
    }
};
