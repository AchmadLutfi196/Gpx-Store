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
        // Cek jika kolom name sudah ada di tabel order_items
        $columns = DB::select("PRAGMA table_info(order_items)");
        $columnNames = array_map(function ($column) {
            return $column->name;
        }, $columns);
        
        // Jika kolom name belum ada, tambahkan
        if (!in_array('name', $columnNames)) {
            DB::statement("ALTER TABLE order_items ADD COLUMN name VARCHAR NULL");
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