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
        Schema::create('airport_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_id')->constrained('airports')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('destination_id')->constrained('airports')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->unique(['origin_id', 'destination_id'], 'uniq_route');

            //Audit
            $table->timestamps();
            $table->foreignId('created_by')->nullable()
                ->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('updated_by')->nullable()
                ->constrained('users')->nullOnDelete()->cascadeOnUpdate();

            // Helpful filter
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airport_routes');
    }
};
