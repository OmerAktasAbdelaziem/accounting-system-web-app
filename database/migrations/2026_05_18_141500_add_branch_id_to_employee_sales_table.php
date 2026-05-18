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
        if (! Schema::hasColumn('employee_sales', 'branch_id')) {
            Schema::table('employee_sales', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('sale_date')->constrained('branches')->nullOnDelete();
                $table->index('branch_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('employee_sales', 'branch_id')) {
            Schema::table('employee_sales', function (Blueprint $table) {
                $table->dropConstrainedForeignId('branch_id');
            });
        }
    }
};
