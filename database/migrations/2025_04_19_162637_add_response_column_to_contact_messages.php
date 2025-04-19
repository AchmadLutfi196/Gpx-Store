<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            // Cek apakah kolom sudah ada sebelum menambahkannya
            if (!Schema::hasColumn('contact_messages', 'admin_response')) {
                $table->text('admin_response')->nullable();
            }
            if (!Schema::hasColumn('contact_messages', 'response_sent')) {
                $table->boolean('response_sent')->default(false);
            }
        });
    }

    public function down()
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['admin_response', 'response_sent']);
        });
    }
};