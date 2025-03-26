<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Buat array untuk menyimpan kolom yang akan ditambahkan
            $columnsToAdd = [];
            
            // Periksa setiap kolom sebelum menambahkannya
            if (!Schema::hasColumn('orders', 'shipping_address')) {
                $columnsToAdd[] = 'shipping_address';
                $table->string('shipping_address')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'shipping_city')) {
                $columnsToAdd[] = 'shipping_city';
                $table->string('shipping_city')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'shipping_state')) {
                $columnsToAdd[] = 'shipping_state';
                $table->string('shipping_state')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'shipping_zipcode')) {
                $columnsToAdd[] = 'shipping_zipcode';
                $table->string('shipping_zipcode')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'shipping_phone')) {
                $columnsToAdd[] = 'shipping_phone';
                $table->string('shipping_phone')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'notes')) {
                $columnsToAdd[] = 'notes';
                $table->text('notes')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $columnsToAdd[] = 'payment_method';
                $table->string('payment_method')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $columnsToAdd[] = 'payment_status';
                $table->string('payment_status')->default('pending');
            }
            
            if (!Schema::hasColumn('orders', 'transaction_id')) {
                $columnsToAdd[] = 'transaction_id';
                $table->string('transaction_id')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'snap_token')) {
                $columnsToAdd[] = 'snap_token';
                $table->string('snap_token')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'shipping_cost')) {
                $columnsToAdd[] = 'shipping_cost';
                $table->decimal('shipping_cost', 12, 2)->default(0);
            }
            
            if (!Schema::hasColumn('orders', 'tax_amount')) {
                $columnsToAdd[] = 'tax_amount';
                $table->decimal('tax_amount', 12, 2)->default(0);
            }
            
            if (!Schema::hasColumn('orders', 'discount_amount')) {
                $columnsToAdd[] = 'discount_amount';
                $table->decimal('discount_amount', 12, 2)->default(0);
            }
            // Log kolom yang ditambahkan
            if (!empty($columnsToAdd)) {
                Log::info('Columns added to orders table: ' . implode(', ', $columnsToAdd));
            } else {
                Log::info('No columns were added to orders table.');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Tidak perlu mendefinisikan down migration karena SQLite tidak mendukung drop column
        });
    }
};