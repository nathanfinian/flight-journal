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
        Schema::create('item_stocks', function (Blueprint $table) {
            $table->id('item_stock_id');
            $table->foreignId('item_id')
                ->constrained('items', 'item_id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->integer('quantity')->default(0);
            $table->timestamp('updated_at')->nullable();

            $table->unique(['item_id', 'branch_id'], 'uniq_item_stock_branch');
            $table->index(['branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_stocks');
    }
};
