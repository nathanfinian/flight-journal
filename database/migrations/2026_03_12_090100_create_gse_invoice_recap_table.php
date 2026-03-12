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
        Schema::create('gse_invoice_recap', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gse_invoice_id')
                ->constrained('gse_invoices')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('gse_recap_id')
                ->constrained('gse_recaps')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('gse_type_rate_id')
                ->nullable()
                ->constrained('gse_type_rates')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->enum('charge_type', ['HOURLY', 'PER_HANDLING']);
            $table->decimal('service_rate', 12, 2);
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('amount', 12, 2);
            $table->timestamps();

            $table->unique(['gse_invoice_id', 'gse_recap_id'], 'uniq_gse_invoice_recap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gse_invoice_recap');
    }
};
