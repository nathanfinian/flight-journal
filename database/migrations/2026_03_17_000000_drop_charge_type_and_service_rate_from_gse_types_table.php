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
        Schema::table('gse_types', function (Blueprint $table) {
            $table->dropColumn(['charge_type', 'service_rate']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gse_types', function (Blueprint $table) {
            $table->decimal('service_rate', 12, 2)->after('service_name');
            $table->enum('charge_type', ['HOURLY', 'PER_HANDLING'])->after('service_rate');
        });
    }
};
