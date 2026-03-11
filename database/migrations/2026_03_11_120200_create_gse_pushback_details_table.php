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
        Schema::create('gse_pushback_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gse_invoice_id')
                ->unique()
                ->constrained('gse_invoices')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('start_ps');
            $table->string('end_ps');
            $table->string('ata');
            $table->string('atd');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gse_pushback_details');
    }
};
