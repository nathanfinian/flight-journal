<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gse_type_rates', function (Blueprint $table) {
            // Legacy rates stay unassigned until their contract owner is confirmed.
            $table->foreignId('airline_id')->nullable()->constrained('airlines')->restrictOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->restrictOnDelete();
            $table->index(
                ['airline_id', 'branch_id', 'gse_type_id', 'effective_from', 'effective_to'],
                'idx_gse_rates_contract_period'
            );
        });
    }

    public function down(): void
    {
        Schema::table('gse_type_rates', function (Blueprint $table) {
            $table->dropIndex('idx_gse_rates_contract_period');
            $table->dropConstrainedForeignId('branch_id');
            $table->dropConstrainedForeignId('airline_id');
        });
    }
};
