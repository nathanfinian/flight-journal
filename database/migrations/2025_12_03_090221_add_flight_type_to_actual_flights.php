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
        Schema::table('actual_flights', function (Blueprint $table) {

            // Add FK at a clean logical position
            $table->foreignId('flight_type_id')
                ->nullable()
                ->after('branch_id')
                ->constrained('flight_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actual_flights', function (Blueprint $table) {
            $table->dropForeign(['flight_type_id']);
            $table->dropColumn('flight_type_id');
        });
    }
};
