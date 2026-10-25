<?php

declare(strict_types=1);

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
        if (! Schema::hasTable('inventory_stocks')) {
            Schema::create('inventory_stocks', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('product_id')
                    ->unique()
                    ->constrained('products')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
                $table->decimal('quantity', 15, 3)->default(0);
                $table->timestamps();
            });
        }

        $hasMovementDirection = Schema::hasColumn(
            'inventory_transactions',
            'movement_direction',
        );
        $hasReferenceType = Schema::hasColumn(
            'inventory_transactions',
            'reference_type',
        );
        $hasReversalOfId = Schema::hasColumn(
            'inventory_transactions',
            'reversal_of_id',
        );

        Schema::table('inventory_transactions', function (Blueprint $table) use (
            $hasMovementDirection,
            $hasReferenceType,
            $hasReversalOfId,
        ): void {
            // Earlier local migrations created quantity as an unsigned integer.
            // The inventory domain supports three decimal places.
            $table->decimal('quantity', 15, 3)->change();

            if (! $hasMovementDirection) {
                $table->string('movement_direction', 10)
                    ->nullable()
                    ->after('transaction_type');
            }

            if (! $hasReferenceType) {
                $table->string('reference_type', 30)
                    ->nullable()
                    ->after('unit');
            }

            if (! $hasReversalOfId) {
                $table->foreignId('reversal_of_id')
                    ->nullable()
                    ->unique()
                    ->after('created_by')
                    ->constrained('inventory_transactions')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();
            }
        });

        if (! $hasMovementDirection) {
            Schema::table('inventory_transactions', function (Blueprint $table): void {
                $table->index('movement_direction');
            });
        }

        if (! $hasReferenceType) {
            Schema::table('inventory_transactions', function (Blueprint $table): void {
                $table->index('reference_type');
            });
        }

        DB::table('inventory_transactions')
            ->whereNull('movement_direction')
            ->update([
                'movement_direction' => DB::raw(
                    "CASE\n                        WHEN transaction_type IN ('initial_stock', 'goods_receipt') THEN 'IN'\n                        WHEN transaction_type = 'goods_issue' THEN 'OUT'\n                        ELSE NULL\n                    END"
                ),
            ]);

        $unresolvedDirections = DB::table('inventory_transactions')
            ->whereNull('movement_direction')
            ->count();

        if ($unresolvedDirections > 0) {
            throw new \RuntimeException(
                'Existing stock adjustments require their historical movement direction to be set before inventory balances can be created.'
            );
        }

        foreach (DB::table('products')->orderBy('id')->get() as $product) {
            if (DB::table('inventory_stocks')
                ->where('product_id', $product->id)
                ->exists()) {
                continue;
            }

            $quantity = DB::table('inventory_transactions')
                ->where('product_id', $product->id)
                ->selectRaw(
                    "COALESCE(SUM(CASE WHEN movement_direction = 'IN' THEN quantity ELSE -quantity END), 0) AS balance"
                )
                ->value('balance');

            DB::table('inventory_stocks')->insert([
                'product_id' => $product->id,
                'quantity' => $quantity ?? '0.000',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('reversal_of_id');
        });

        Schema::dropIfExists('inventory_stocks');
    }
};
