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
        if (Schema::hasTable('employee_sales') && ! Schema::hasColumn('employee_sales', 'employee_assignments')) {
            Schema::table('employee_sales', function (Blueprint $table) {
                $table->json('employee_assignments')->nullable()->after('notes_ar');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('employee_sales') && Schema::hasColumn('employee_sales', 'employee_assignments')) {
            Schema::table('employee_sales', function (Blueprint $table) {
                $table->dropColumn('employee_assignments');
            });
        }
    }
};
