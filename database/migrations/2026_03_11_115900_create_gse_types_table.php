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
        Schema::create('gse_types', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->decimal('service_rate', 12, 2);
            $table->enum('charge_type', ['HOURLY', 'PER_HANDLING']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gse_types');
    }
};
