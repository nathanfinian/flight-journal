<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (
            Schema::hasColumn('gse_invoices', 'branch_id')
            && Schema::hasColumn('gse_invoices', 'airline_id')
        ) {
            return;
        }

        if (! Schema::hasColumn('gse_invoices', 'branch_id')) {
            Schema::table('gse_invoices', function (Blueprint $table) {
                $table->foreignId('branch_id')
                    ->nullable()
                    ->after('gse_type_id');
            });
        }

        if (! $this->foreignKeyExists('fk_gse_invoice_header_branch')) {
            Schema::table('gse_invoices', function (Blueprint $table) {
                $table->foreign('branch_id', 'fk_gse_invoice_header_branch')
                    ->references('id')
                    ->on('branches')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();
            });
        }

        if (! Schema::hasColumn('gse_invoices', 'airline_id')) {
            Schema::table('gse_invoices', function (Blueprint $table) {
                $table->foreignId('airline_id')
                    ->nullable()
                    ->after('branch_id');
            });
        }

        if (! $this->foreignKeyExists('fk_gse_invoice_header_airline')) {
            Schema::table('gse_invoices', function (Blueprint $table) {
                $table->foreign('airline_id', 'fk_gse_invoice_header_airline')
                    ->references('id')
                    ->on('airlines')
                    ->restrictOnDelete()
                    ->cascadeOnUpdate();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (
            ! Schema::hasColumn('gse_invoices', 'branch_id')
            && ! Schema::hasColumn('gse_invoices', 'airline_id')
        ) {
            return;
        }

        if (Schema::hasColumn('gse_invoices', 'airline_id')) {
            Schema::table('gse_invoices', function (Blueprint $table) {
                if ($this->foreignKeyExists('fk_gse_invoice_header_airline')) {
                    $table->dropForeign('fk_gse_invoice_header_airline');
                }

                $table->dropColumn('airline_id');
            });
        }

        if (Schema::hasColumn('gse_invoices', 'branch_id')) {
            Schema::table('gse_invoices', function (Blueprint $table) {
                if ($this->foreignKeyExists('fk_gse_invoice_header_branch')) {
                    $table->dropForeign('fk_gse_invoice_header_branch');
                }

                $table->dropColumn('branch_id');
            });
        }
    }

    private function foreignKeyExists(string $name): bool
    {
        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::raw('DATABASE()'))
            ->where('TABLE_NAME', 'gse_invoices')
            ->where('CONSTRAINT_NAME', $name)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->exists();
    }
};
