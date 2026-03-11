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
        Schema::table('gse_invoices', function (Blueprint $table) {
            $table->foreignId('branch_id')
                ->after('gse_type_id')
                ->constrained('branches')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('airline_id')
                ->after('branch_id')
                ->constrained('airlines')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gse_invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('airline_id');
            $table->dropConstrainedForeignId('branch_id');
        });
    }
};
