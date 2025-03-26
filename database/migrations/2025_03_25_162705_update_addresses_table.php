<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if address table exists
        if (Schema::hasTable('addresses')) {
            // Check for missing columns and add them if needed
            Schema::table('addresses', function (Blueprint $table) {
                if (!Schema::hasColumn('addresses', 'is_default')) {
                    $table->boolean('is_default')->default(false);
                }
                // Add other missing columns if needed
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // If you need to undo these changes
        if (Schema::hasTable('addresses')) {
            Schema::table('addresses', function (Blueprint $table) {
                // Remove columns added in this migration if needed
                // $table->dropColumn('is_default');
            });
        }
    }
};