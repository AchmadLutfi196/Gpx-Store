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
            // Periksa apakah kolom description belum ada
            if (!Schema::hasColumn('promo_codes', 'description')) {
                $table->text('description')->nullable()->after('code');
            }
            
            // Periksa apakah kolom discount_type belum ada
            if (!Schema::hasColumn('promo_codes', 'discount_type')) {
                $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage')->after('description');
            }
            
            // Periksa apakah kolom minimum_order belum ada
            if (!Schema::hasColumn('promo_codes', 'minimum_order')) {
                $table->decimal('minimum_order', 10, 2)->default(0)->after('discount_value');
            }
            
            // Periksa apakah kolom maximum_discount belum ada
            if (!Schema::hasColumn('promo_codes', 'maximum_discount')) {
                $table->decimal('maximum_discount', 10, 2)->default(0)->comment('For percentage discounts')->after('minimum_order');
            }
            
            // Periksa apakah kolom start_date belum ada
            if (!Schema::hasColumn('promo_codes', 'start_date')) {
                $table->timestamp('start_date')->nullable()->after('maximum_discount');
            }
            
            // Periksa apakah kolom end_date belum ada
            if (!Schema::hasColumn('promo_codes', 'end_date')) {
                $table->timestamp('end_date')->nullable()->after('start_date');
            }
            
            // Periksa apakah kolom is_active belum ada
            if (!Schema::hasColumn('promo_codes', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('end_date');
            }
            
            // Periksa apakah kolom usage_limit belum ada
            if (!Schema::hasColumn('promo_codes', 'usage_limit')) {
                $table->integer('usage_limit')->default(0)->comment('0 means unlimited')->after('is_active');
            }
            
            // Periksa apakah kolom used_count belum ada
            if (!Schema::hasColumn('promo_codes', 'used_count')) {
                $table->integer('used_count')->default(0)->after('usage_limit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            // Karena ini migrasi untuk menambahkan kolom yang belum ada,
            // jika perlu di-rollback, kita bisa drop kolom-kolom tersebut
            $columns = [
                'description', 'discount_type', 'minimum_order', 'maximum_discount',
                'start_date', 'end_date', 'is_active', 'usage_limit', 'used_count'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('promo_codes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};