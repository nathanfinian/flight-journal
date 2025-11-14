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
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom dulu
            $table->foreignId('role_id')
                ->nullable()                       // boleh null kalau optional
                ->after('id')                      // letakkan setelah kolom id
                ->constrained('roles')          // FK ke table "roles"
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('branch_id')
                ->nullable()                       // boleh null kalau optional
                ->after('role_id')
                ->constrained('branches')          // FK ke table "branches"
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['role_id']);

            // Then drop columns
            $table->dropColumn(['branch_id', 'role_id']);
        });
    }
};
