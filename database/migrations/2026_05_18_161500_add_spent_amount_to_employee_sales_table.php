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
        if (Schema::hasTable('employee_sales') && ! Schema::hasColumn('employee_sales', 'spent_amount')) {
            Schema::table('employee_sales', function (Blueprint $table) {
                $table->decimal('spent_amount', 15, 2)->default(0)->after('total_amount');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('employee_sales') && Schema::hasColumn('employee_sales', 'spent_amount')) {
            Schema::table('employee_sales', function (Blueprint $table) {
                $table->dropColumn('spent_amount');
            });
        }
    }
};
