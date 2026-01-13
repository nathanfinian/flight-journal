<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {

            $table->foreignId('airport_id')
                ->nullable()
                ->after('status')          // optional: placement
                ->constrained('airports')  // assumes airports table
                ->cascadeOnUpdate()
                ->restrictOnDelete();      // safer than cascade
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {

            $table->dropForeign(['airport_id']);
            $table->dropColumn('airport_id');
        });
    }
};
