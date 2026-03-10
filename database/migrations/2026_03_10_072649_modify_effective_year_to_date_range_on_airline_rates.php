<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('airline_rates', function (Blueprint $table) {

            // Remove old column
            $table->dropColumn('effective_year');

            // Add date range columns
            $table->date('date_from')
                ->nullable()
                ->after('charge_code');

            $table->date('date_to')
                ->nullable()
                ->after('date_from');
                
            $table->index(['airline_id', 'date_from', 'date_to']);
        });
    }

    public function down(): void
    {
        Schema::table('airline_rates', function (Blueprint $table) {

            // Remove new columns
            $table->dropColumn([
                'date_from',
                'date_to',
            ]);

            // Restore old column
            $table->unsignedSmallInteger('effective_year')
                ->nullable()
                ->after('charge_code');
        });
    }
};
