<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNewslettersTableStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First check if the table exists
        if (Schema::hasTable('newsletters')) {
            // Check if the email column exists
            if (Schema::hasColumn('newsletters', 'email')) {
                Schema::table('newsletters', function (Blueprint $table) {
                    $table->string('email')->nullable()->change();
                });
            }
        }
        // If the table doesn't exist yet, create it with the right structure
        else {
            Schema::create('newsletters', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('content');
                $table->string('email')->nullable();
                $table->enum('status', ['draft', 'scheduled', 'sent'])->default('draft');
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
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
        // This is a potentially destructive operation, so we'll just
        // restore the NOT NULL constraint on email if it exists
        if (Schema::hasTable('newsletters') && Schema::hasColumn('newsletters', 'email')) {
            Schema::table('newsletters', function (Blueprint $table) {
                $table->string('email')->nullable(false)->change();
            });
        }
    }
}
