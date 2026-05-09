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
        Schema::create('gse_equipment', function (Blueprint $table) {
            $table->id('gse_equipment_id');
            $table->string('equipment_code', 100)->unique();
            $table->foreignId('gse_type_id')
                ->constrained('gse_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('name', 255);
            $table->string('serial_number', 100)->nullable();
            $table->string('asset_number', 100)->nullable();
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->integer('manufacture_year')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('total_hours_used', 10, 2)->nullable();
            $table->string('status', 20);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['gse_type_id', 'status']);
            $table->index(['branch_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gse_equipment');
    }
};
