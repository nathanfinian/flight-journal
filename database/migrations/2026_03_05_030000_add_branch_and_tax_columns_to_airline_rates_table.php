<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('airline_rates', function (Blueprint $table) {
            $table->foreignId('branch_id')
                ->nullable()
                ->after('airline_id')
                ->constrained('branches')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedSmallInteger('effective_year')
                ->nullable()
                ->after('charge_code');

            $table->decimal('ppn_rate', 5, 4)
                ->nullable()
                ->default(0)
                ->after('cargo_fee');

            $table->decimal('pph_rate', 5, 4)
                ->nullable()
                ->default(0)
                ->after('ppn_rate');

            $table->decimal('konsesi_rate', 5, 4)
                ->nullable()
                ->default(0)
                ->after('pph_rate');
        });
    }

    public function down(): void
    {
        Schema::table('airline_rates', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn([
                'branch_id',
                'effective_year',
                'ppn_rate',
                'pph_rate',
                'konsesi_rate',
            ]);
        });
    }
};

