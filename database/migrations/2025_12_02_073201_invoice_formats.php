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
        Schema::create('invoice_formats', function (Blueprint $table) {
            $table->id();

            // Basic identification
            $table->string('name');
            
            // Short code you can reference in code (e.g. FLT_STD, GSE_GARUDA, etc)
            $table->string('code')->unique();

            // Airline specific (optional)
            $table->foreignId('airline_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Branch issuing the invoice
            $table->foreignId('branch_id')
                ->constrained()
                ->cascadeOnDelete();

            // Invoice header (e.g., "PT Bina Angkasa", "Flight Journal Invoice")
            $table->string('header')->nullable();

            // Contact information
            $table->string('address')->nullable();
            $table->string('phone_number', 50)->nullable();
            $table->string('email')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_formats');
    }
};
