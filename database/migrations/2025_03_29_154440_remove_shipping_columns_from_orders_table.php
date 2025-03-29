<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveShippingColumnsFromOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Remove the individual shipping columns
            if (Schema::hasColumn('orders', 'shipping_city')) {
                $table->dropColumn('shipping_city');
            }
            // Add other shipping columns that need to be removed
            if (Schema::hasColumn('orders', 'shipping_address_line1')) {
                $table->dropColumn('shipping_address_line1');
            }
            if (Schema::hasColumn('orders', 'shipping_state')) {
                $table->dropColumn('shipping_state');
            }
            if (Schema::hasColumn('orders', 'shipping_postal_code')) {
                $table->dropColumn('shipping_postal_code');
            }
            if (Schema::hasColumn('orders', 'recipient_name')) {
                $table->dropColumn('recipient_name');
            }
            if (Schema::hasColumn('orders', 'recipient_phone')) {
                $table->dropColumn('recipient_phone');
            }
            
            // Make sure we have the JSON column for shipping_address
            if (!Schema::hasColumn('orders', 'shipping_address')) {
                $table->json('shipping_address')->nullable();
            }
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
            // Add back the individual shipping columns
            $table->string('shipping_city')->nullable();
            $table->string('shipping_address_line1')->nullable();
            $table->string('shipping_state')->nullable();
            $table->string('shipping_postal_code')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone')->nullable();
        });
    }
}