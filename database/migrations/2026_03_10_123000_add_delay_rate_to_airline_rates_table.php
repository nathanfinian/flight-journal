<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('airline_rates', function (Blueprint $table) {
            $table->decimal('delay_rate', 15, 2)
                ->nullable()
                ->after('ground_fee');
        });
    }

    public function down(): void
    {
        Schema::table('airline_rates', function (Blueprint $table) {
            $table->dropColumn('delay_rate');
        });
    }
};
