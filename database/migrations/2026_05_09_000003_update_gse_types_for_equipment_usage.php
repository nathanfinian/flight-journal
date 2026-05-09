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
        if (Schema::hasColumn('gse_types', 'service_name') && ! Schema::hasColumn('gse_types', 'type_name')) {
            Schema::table('gse_types', function (Blueprint $table) {
                $table->renameColumn('service_name', 'type_name');
            });
        }

        if (! Schema::hasColumn('gse_types', 'description')) {
            Schema::table('gse_types', function (Blueprint $table) {
                $table->text('description')->nullable()->after('type_name');
            });
        }

        if (! Schema::hasColumn('gse_types', 'created_by')) {
            Schema::table('gse_types', function (Blueprint $table) {
                $table->foreignId('created_by')
                    ->nullable()
                    ->after('description')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('gse_types', 'updated_by')) {
            Schema::table('gse_types', function (Blueprint $table) {
                $table->foreignId('updated_by')
                    ->nullable()
                    ->after('created_by')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('gse_types', 'updated_by')) {
            Schema::table('gse_types', function (Blueprint $table) {
                $table->dropConstrainedForeignId('updated_by');
            });
        }

        if (Schema::hasColumn('gse_types', 'created_by')) {
            Schema::table('gse_types', function (Blueprint $table) {
                $table->dropConstrainedForeignId('created_by');
            });
        }

        if (Schema::hasColumn('gse_types', 'description')) {
            Schema::table('gse_types', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        if (Schema::hasColumn('gse_types', 'type_name') && ! Schema::hasColumn('gse_types', 'service_name')) {
            Schema::table('gse_types', function (Blueprint $table) {
                $table->renameColumn('type_name', 'service_name');
            });
        }
    }
};
