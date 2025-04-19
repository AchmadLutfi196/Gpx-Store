<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('contact_messages', 'admin_response')) {
                $table->text('admin_response')->nullable();
            }
            if (!Schema::hasColumn('contact_messages', 'responded_at')) {
                $table->timestamp('responded_at')->nullable();
            }
            if (!Schema::hasColumn('contact_messages', 'responded_by')) {
                $table->unsignedBigInteger('responded_by')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['admin_response', 'responded_at', 'responded_by']);
        });
    }
};