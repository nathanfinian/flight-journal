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
        Schema::create('days', function (Blueprint $table) {
            $table->id();                            // day_id
            $table->string('day_name', 16)->unique(); // e.g. Monday, Tuesday, ...
            $table->unsignedTinyInteger('dow')->unique(); // 0=Sunday .. 6=Saturday
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('days');
    }
};
