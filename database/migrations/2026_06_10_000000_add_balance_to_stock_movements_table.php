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
        Schema::table('stock_movements', function (Blueprint $table): void {
            $table->integer('balance')->default(0)->after('quantity');
        });

        DB::table('stock_movements')
            ->select('item_id', 'branch_id')
            ->distinct()
            ->orderBy('item_id')
            ->orderBy('branch_id')
            ->get()
            ->each(function ($pair): void {
                $balance = (int) (DB::table('item_stocks')
                    ->where('item_id', $pair->item_id)
                    ->where('branch_id', $pair->branch_id)
                    ->value('quantity') ?? 0);

                DB::table('stock_movements')
                    ->where('item_id', $pair->item_id)
                    ->where('branch_id', $pair->branch_id)
                    ->orderByDesc('movement_date')
                    ->orderByDesc('movement_id')
                    ->get(['movement_id', 'movement_type', 'quantity'])
                    ->each(function ($movement) use (&$balance): void {
                        DB::table('stock_movements')
                            ->where('movement_id', $movement->movement_id)
                            ->update(['balance' => $balance]);

                        $delta = $movement->movement_type === 'INPUT'
                            ? (int) $movement->quantity
                            : -1 * (int) $movement->quantity;

                        $balance -= $delta;
                    });
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table): void {
            $table->dropColumn('balance');
        });
    }
};
