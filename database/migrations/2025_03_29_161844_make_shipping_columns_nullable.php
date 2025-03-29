<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeShippingColumnsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Ubah semua kolom shipping menjadi nullable
            $table->string('shipping_postal_code')->nullable()->change();
            $table->string('shipping_phone')->nullable()->change();
            
            // Tambahkan kolom-kolom shipping lainnya yang mungkin ada
            // Contoh:
            $table->string('shipping_city')->nullable()->change();
            $table->string('shipping_state')->nullable()->change();
            $table->string('shipping_address_line1')->nullable()->change();
            $table->string('recipient_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Kembalikan kolom-kolom ke NOT NULL jika diperlukan
            $table->string('shipping_postal_code')->nullable(false)->change();
            $table->string('shipping_phone')->nullable(false)->change();
            
            // Kolom lainnya juga
            $table->string('shipping_city')->nullable(false)->change();
            $table->string('shipping_state')->nullable(false)->change();
            $table->string('shipping_address_line1')->nullable(false)->change();
            $table->string('recipient_name')->nullable(false)->change();
        });
    }
}