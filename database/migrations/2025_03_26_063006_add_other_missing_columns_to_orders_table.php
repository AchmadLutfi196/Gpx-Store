<?php

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
        // Kolom-kolom yang perlu ditambahkan
        $columns = [
            'shipping_address' => 'VARCHAR NULL',
            'shipping_city' => 'VARCHAR NULL',
            'shipping_state' => 'VARCHAR NULL',
            'shipping_zipcode' => 'VARCHAR NULL',
            'shipping_phone' => 'VARCHAR NULL',
            'notes' => 'TEXT NULL',
            'payment_status' => 'VARCHAR DEFAULT "pending" NULL',
            'transaction_id' => 'VARCHAR NULL',
            'snap_token' => 'VARCHAR NULL',
            'shipping_cost' => 'DECIMAL(12, 2) DEFAULT 0 NULL',
            'tax_amount' => 'DECIMAL(12, 2) DEFAULT 0 NULL',
            'discount_amount' => 'DECIMAL(12, 2) DEFAULT 0 NULL',
        ];

        // Tambahkan kolom-kolom yang belum ada
        if (DB::connection()->getDriverName() === 'sqlite') {
            foreach ($columns as $column => $definition) {
                // Cek apakah kolom sudah ada
                $result = DB::select("PRAGMA table_info(orders)");
                $columnExists = false;
                
                foreach ($result as $col) {
                    if ($col->name === $column) {
                        $columnExists = true;
                        break;
                    }
                }
                
                // Jika belum ada, tambahkan
                if (!$columnExists) {
                    DB::statement("ALTER TABLE orders ADD COLUMN {$column} {$definition}");
                }
            }
        } else {
            // Untuk database lain
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'shipping_address')) {
                    $table->string('shipping_address')->nullable();
                }
                // Tambahkan kolom lainnya di sini
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak diperlukan untuk SQLite
    }
};