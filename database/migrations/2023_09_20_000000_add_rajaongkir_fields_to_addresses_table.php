<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRajaongkirFieldsToAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Add RajaOngkir specific fields if they don't exist
            if (!Schema::hasColumn('addresses', 'city_id')) {
                $table->string('city_id')->nullable()->after('city');
            }
            
            if (!Schema::hasColumn('addresses', 'province_id')) {
                $table->string('province_id')->nullable()->after('province');
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
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['city_id', 'province_id']);
        });
    }
}
