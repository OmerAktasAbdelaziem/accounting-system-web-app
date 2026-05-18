<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            try {
                DB::statement('ALTER TABLE products DROP INDEX products_name_name_ar_sku_fulltext');
            } catch (\Throwable) {
            }

            // Drop the unique index on `sku` if present. Use DROP INDEX (SQLite) or ALTER TABLE (MySQL)
            try {
                // SQLite supports: DROP INDEX IF EXISTS index_name
                DB::statement('DROP INDEX IF EXISTS products_sku_unique');
            } catch (\Throwable) {
                try {
                    DB::statement('ALTER TABLE products DROP INDEX products_sku_unique');
                } catch (\Throwable) {
                }
            }
        }

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'sku')) {
                $table->dropColumn('sku');
            }

            if (Schema::hasColumn('products', 'min_stock')) {
                $table->dropColumn('min_stock');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->unique()->after('name_ar');
            }

            if (! Schema::hasColumn('products', 'min_stock')) {
                $table->integer('min_stock')->default(10)->after('profit_margin');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            $table->fullText(['name', 'name_ar', 'sku']);
        });
    }
};