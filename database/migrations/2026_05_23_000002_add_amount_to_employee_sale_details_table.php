<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('employee_sale_details') && ! Schema::hasColumn('employee_sale_details', 'amount')) {
            Schema::table('employee_sale_details', function (Blueprint $table) {
                $table->decimal('amount', 12, 2)->default(0)->after('employee_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('employee_sale_details') && Schema::hasColumn('employee_sale_details', 'amount')) {
            Schema::table('employee_sale_details', function (Blueprint $table) {
                $table->dropColumn('amount');
            });
        }
    }
};