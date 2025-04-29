<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            if (!Schema::hasColumn('promo_codes', 'show_on_homepage')) {
                $table->boolean('show_on_homepage')->default(false);
            }
            
            if (!Schema::hasColumn('promo_codes', 'promotion_title')) {
                $table->string('promotion_title')->nullable();
            }
            
            if (!Schema::hasColumn('promo_codes', 'promotion_subtitle')) {
                $table->string('promotion_subtitle')->nullable();
            }
            
            if (!Schema::hasColumn('promo_codes', 'promotion_tag')) {
                $table->string('promotion_tag')->nullable(); // "PENAWARAN TERBATAS", etc.
            }
            
            if (!Schema::hasColumn('promo_codes', 'promotion_note')) {
                $table->string('promotion_note')->nullable(); // "*Batas penggunaan per akun..."
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->dropColumn([
                'show_on_homepage',
                'promotion_title',
                'promotion_subtitle',
                'promotion_tag',
                'promotion_note'
            ]);
        });
    }
};