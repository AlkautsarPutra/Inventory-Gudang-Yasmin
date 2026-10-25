<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereIn('role', ['warehouse_manager', 'warehouse_staff'])
            ->update(['role' => 'admin']);
        
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 30)->default('viewer')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 30)->default('warehouse_staff')->change();
        });
    }
};
