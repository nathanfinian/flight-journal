<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('actual_flights', function (Blueprint $table) {

            // 1. Drop old unique indexes
            $table->dropUnique('uniq_origin_flight_instance');
            $table->dropUnique('uniq_departure_flight_instance');

            // 2. Add new unique indexes including deleted_at
            $table->unique(
                ['origin_flight_no', 'service_date', 'deleted_at'],
                'uniq_origin_flight_instance'
            );

            $table->unique(
                ['departure_flight_no', 'service_date', 'deleted_at'],
                'uniq_departure_flight_instance'
            );
        });
    }

    public function down(): void
    {
        Schema::table('actual_flights', function (Blueprint $table) {

            // Rollback: drop new constraints
            $table->dropUnique('uniq_origin_flight_instance');
            $table->dropUnique('uniq_departure_flight_instance');

            // Restore original constraints (without deleted_at)
            $table->unique(
                ['origin_flight_no', 'service_date'],
                'uniq_origin_flight_instance'
            );

            $table->unique(
                ['departure_flight_no', 'service_date'],
                'uniq_departure_flight_instance'
            );
        });
    }
};
