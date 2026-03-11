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
        Schema::create('gse_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gse_type_id')
                ->constrained('gse_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->date('service_date');
            $table->foreignId('equipment_id')
                ->constrained('equipments')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('invoice_number')->unique();
            $table->string('flight_number', 10);
            $table->string('er_number')->unique();
            $table->string('operator_name');
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gse_invoices');
    }
};
