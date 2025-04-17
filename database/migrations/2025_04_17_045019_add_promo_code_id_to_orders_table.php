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
        // Cek jika tabel orders ada
        if (Schema::hasTable('orders')) {
            // Dapatkan informasi kolom yang ada
            $columns = DB::select("PRAGMA table_info(orders)");
            $columnNames = array_map(function ($column) {
                return $column->name;
            }, $columns);
            
            // Tambahkan kolom promo_code_id jika belum ada
            if (!in_array('promo_code_id', $columnNames)) {
                DB::statement("ALTER TABLE orders ADD COLUMN promo_code_id INTEGER NULL");
            }
            
            // Pastikan kolom discount_amount ada
            if (!in_array('discount_amount', $columnNames)) {
                DB::statement("ALTER TABLE orders ADD COLUMN discount_amount DECIMAL(10,2) DEFAULT 0");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // SQLite tidak mendukung drop column
    }
};