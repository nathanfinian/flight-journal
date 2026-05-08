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
        Schema::table('gse_invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('gse_invoices', 'toWhom')) {
                $table->string('toWhom')->nullable()->after('airline_id');
            }

            if (! Schema::hasColumn('gse_invoices', 'toTitle')) {
                $table->string('toTitle')->nullable()->after('toWhom');
            }

            if (! Schema::hasColumn('gse_invoices', 'toCompany')) {
                $table->string('toCompany')->nullable()->after('toTitle');
            }

            if (! Schema::hasColumn('gse_invoices', 'signer_name')) {
                $table->string('signer_name')->nullable()->after('toCompany');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gse_invoices', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('gse_invoices', 'signer_name') ? 'signer_name' : null,
                Schema::hasColumn('gse_invoices', 'toCompany') ? 'toCompany' : null,
                Schema::hasColumn('gse_invoices', 'toTitle') ? 'toTitle' : null,
                Schema::hasColumn('gse_invoices', 'toWhom') ? 'toWhom' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
