<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('employee_sales')) {
            DB::statement('ALTER TABLE employee_sales MODIFY employee_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE employee_sales MODIFY product_id BIGINT UNSIGNED NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql' && Schema::hasTable('employee_sales')) {
            DB::statement('ALTER TABLE employee_sales MODIFY employee_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE employee_sales MODIFY product_id BIGINT UNSIGNED NOT NULL');
        }
    }
};
