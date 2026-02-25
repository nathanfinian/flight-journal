<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposit_receipts', function (Blueprint $table) {
            $table->id();

            // Receipt Info
            $table->string('receipt_number')->unique();
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->restrictOnDelete();

            // Main Data
            $table->string('received_from_company');
            $table->string('signer_name');
            $table->date('receipt_date');
            $table->text('description')->nullable();
            $table->decimal('value', 18, 2);

            // Audit Fields
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposit_receipts');
    }
};
