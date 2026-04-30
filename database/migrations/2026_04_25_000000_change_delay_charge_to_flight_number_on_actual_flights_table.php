<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('actual_flights', function (Blueprint $table) {
            $table->string('delay_charge_flight_no', 10)
                ->nullable()
                ->after('delay_charge');
        });

        DB::table('actual_flights')
            ->where('delay_charge', true)
            ->update([
                'delay_charge_flight_no' => DB::raw('origin_flight_no'),
            ]);

        Schema::table('actual_flights', function (Blueprint $table) {
            $table->dropColumn('delay_charge');
        });

        Schema::table('actual_flights', function (Blueprint $table) {
            $table->renameColumn('delay_charge_flight_no', 'delay_charge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actual_flights', function (Blueprint $table) {
            $table->boolean('delay_charge_flag')
                ->default(false)
                ->after('delay_charge');
        });

        DB::table('actual_flights')
            ->whereNotNull('delay_charge')
            ->where('delay_charge', '!=', '')
            ->update([
                'delay_charge_flag' => true,
            ]);

        Schema::table('actual_flights', function (Blueprint $table) {
            $table->dropColumn('delay_charge');
        });

        Schema::table('actual_flights', function (Blueprint $table) {
            $table->renameColumn('delay_charge_flag', 'delay_charge');
        });
    }
};
