<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNewslettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('newsletters', function (Blueprint $table) {
            // Add new fields for newsletter campaigns
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->enum('status', ['active', 'unsubscribed', 'draft', 'scheduled', 'sent'])->default('active')->change();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->dropColumn(['title', 'content', 'scheduled_at', 'sent_at']);
            $table->enum('status', ['active', 'unsubscribed'])->default('active')->change();
        });
    }
}
