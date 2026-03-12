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
        if (Schema::hasColumn('gse_gpu_details', 'gse_invoice_id')) {
            try {
                Schema::table('gse_gpu_details', function (Blueprint $table) {
                    $table->dropForeign(['gse_invoice_id']);
                });
            } catch (\Throwable $e) {
                // Ignore if the FK was already dropped during a failed prior run.
            }

            Schema::table('gse_gpu_details', function (Blueprint $table) {
                $table->renameColumn('gse_invoice_id', 'gse_recap_id');
            });
        }

        if (! Schema::hasColumn('gse_gpu_details', 'gse_invoice_id') && ! Schema::hasColumn('gse_gpu_details', 'gse_recap_id')) {
            Schema::table('gse_gpu_details', function (Blueprint $table) {
                $table->unsignedBigInteger('gse_recap_id')->nullable()->after('id');
            });
        }

        if (Schema::hasColumn('gse_gpu_details', 'gse_recap_id')) {
            Schema::table('gse_gpu_details', function (Blueprint $table) {
                $table->foreign('gse_recap_id')
                    ->references('id')
                    ->on('gse_recaps')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('gse_pushback_details', 'gse_invoice_id')) {
            try {
                Schema::table('gse_pushback_details', function (Blueprint $table) {
                    $table->dropForeign(['gse_invoice_id']);
                });
            } catch (\Throwable $e) {
                // Ignore if the FK was already dropped during a failed prior run.
            }

            Schema::table('gse_pushback_details', function (Blueprint $table) {
                $table->renameColumn('gse_invoice_id', 'gse_recap_id');
            });
        }

        if (! Schema::hasColumn('gse_pushback_details', 'gse_invoice_id') && ! Schema::hasColumn('gse_pushback_details', 'gse_recap_id')) {
            Schema::table('gse_pushback_details', function (Blueprint $table) {
                $table->unsignedBigInteger('gse_recap_id')->nullable()->after('id');
            });
        }

        if (Schema::hasColumn('gse_pushback_details', 'gse_recap_id')) {
            Schema::table('gse_pushback_details', function (Blueprint $table) {
                $table->foreign('gse_recap_id')
                    ->references('id')
                    ->on('gse_recaps')
                    ->cascadeOnUpdate()
                    ->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gse_pushback_details', function (Blueprint $table) {
            $table->dropForeign(['gse_recap_id']);
        });

        Schema::table('gse_pushback_details', function (Blueprint $table) {
            $table->renameColumn('gse_recap_id', 'gse_invoice_id');
        });

        Schema::table('gse_pushback_details', function (Blueprint $table) {
            $table->foreign('gse_invoice_id')
                ->references('id')
                ->on('gse_invoices')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        Schema::table('gse_gpu_details', function (Blueprint $table) {
            $table->dropForeign(['gse_recap_id']);
        });

        Schema::table('gse_gpu_details', function (Blueprint $table) {
            $table->renameColumn('gse_recap_id', 'gse_invoice_id');
        });

        Schema::table('gse_gpu_details', function (Blueprint $table) {
            $table->foreign('gse_invoice_id')
                ->references('id')
                ->on('gse_invoices')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }
};
