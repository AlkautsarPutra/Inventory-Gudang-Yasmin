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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Supplier Identity
            |--------------------------------------------------------------------------
            */

            $table->string('supplier_code', 50)->unique();
            $table->string('supplier_name', 150);

            /*
            |--------------------------------------------------------------------------
            | Company Information
            |--------------------------------------------------------------------------
            */

            $table->string('company_name', 150)->nullable();
            $table->string('tax_identification_number', 50)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Contact Information
            |--------------------------------------------------------------------------
            */

            $table->string('contact_person', 100)->nullable();
            $table->string('phone_number', 30)->nullable();
            $table->string('mobile_number', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('website', 255)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Business Address
            |--------------------------------------------------------------------------
            */

            $table->text('street_address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 100)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Business Classification
            |--------------------------------------------------------------------------
            */

            $table->string('supplier_category', 100)->nullable();
            $table->string('business_type', 100)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->string('status', 20)->default('ACTIVE');

            /*
            |--------------------------------------------------------------------------
            | Timestamps
            |--------------------------------------------------------------------------
            */

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('supplier_name');
            $table->index('company_name');
            $table->index('supplier_category');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};