<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('alert_type', 30); // 'below_rop', 'below_safety', 'out_of_stock'
            $table->decimal('current_stock', 15, 3);
            $table->decimal('threshold', 15, 3);
            $table->boolean('is_acknowledged')->default(false);
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('acknowledged_at')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'is_acknowledged']);
            $table->index('alert_type');
            $table->index('is_acknowledged');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_alerts');
    }
};
