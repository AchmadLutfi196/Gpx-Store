<?php
// filepath: c:\laragon\www\gpx-store\database\migrations\2025_04_17_045019_add_promo_code_id_to_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            // Use Laravel's Schema builder instead of raw SQL
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'promo_code_id')) {
                    $table->unsignedBigInteger('promo_code_id')->nullable();
                    
                    // Add foreign key constraint if promo_codes table exists
                    if (Schema::hasTable('promo_codes')) {
                        $table->foreign('promo_code_id')
                              ->references('id')
                              ->on('promo_codes')
                              ->onDelete('set null');
                    }
                }
                
                if (!Schema::hasColumn('orders', 'discount_amount')) {
                    $table->decimal('discount_amount', 10, 2)->default(0);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign key constraint if it exists
            // For MySQL, we can use dropForeignIfExists instead of checking via Doctrine
            if (Schema::hasColumn('orders', 'promo_code_id')) {
                // The constraint name is likely to follow Laravel's naming convention
                $table->dropForeignIfExists(['promo_code_id']);
                $table->dropColumn('promo_code_id');
            }
            
            if (Schema::hasColumn('orders', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
        });
    }
};