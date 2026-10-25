<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demand_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('period', 7);               // YYYY-MM
            $table->decimal('actual_demand', 15, 3)->nullable();
            $table->decimal('forecasted_demand', 15, 3);
            $table->decimal('alpha', 5, 4);             // smoothing constant
            $table->decimal('mae', 15, 3)->nullable();  // Mean Absolute Error
            $table->decimal('mape', 8, 4)->nullable();  // Mean Absolute Percentage Error (%)
            $table->timestamps();

            $table->unique(['product_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demand_forecasts');
    }
};
