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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id('movement_id');
            $table->foreignId('item_id')
                ->constrained('items', 'item_id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('gse_equipment_id')
                ->nullable()
                ->constrained('gse_equipment', 'gse_equipment_id')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->string('movement_type', 20);
            $table->integer('quantity');
            $table->dateTime('movement_date');
            $table->string('reference_no', 100)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['item_id', 'movement_date']);
            $table->index(['branch_id', 'movement_date']);
            $table->index(['gse_equipment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
