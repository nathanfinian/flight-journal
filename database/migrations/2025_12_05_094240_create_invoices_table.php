<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Identification
            $table->string('title'); 
            $table->string('invoice_number')->unique();

            // Date range
            $table->date('date');
            $table->date('dateFrom');
            $table->date('dateTo');

            // Airline (optional)
            $table->foreignId('airline_id')
                ->nullable()
                ->constrained('airlines')
                ->nullOnDelete();

            // Branch issuing the invoice
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnDelete();

            // Rate used for calculation
            $table->foreignId('airline_rates_id')
                ->constrained('airline_rates')
                ->cascadeOnDelete();

            // Invoice management fields
            $table->enum('status', ['ISSUED', 'PAID', 'CANCELLED'])->default('ISSUED');
            $table->date('due_date')->nullable();
            $table->decimal('total_amount', 12, 2)->nullable();

            // Audit fields
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

            $table->foreignId('deleted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
