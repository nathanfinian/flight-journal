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
        Schema::create('items', function (Blueprint $table) {
            $table->id('item_id');
            $table->string('code', 100)->unique();
            $table->foreignId('sub_category_id')
                ->constrained('sub_categories', 'sub_category_id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('name', 255);
            $table->foreignId('unit_id')
                ->constrained('units', 'unit_id')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->integer('minimum_stock')->default(0);
            $table->string('status', 20);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['sub_category_id', 'status']);
            $table->index(['unit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
