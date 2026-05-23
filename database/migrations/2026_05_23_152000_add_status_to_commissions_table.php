<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasColumn('commissions', 'status')) {
            Schema::table('commissions', function (Blueprint $table) {
                $table->string('status', 50)->default('pending')->after('id');
            });
        }

        if (!Schema::hasColumn('employee_commissions', 'status')) {
            Schema::table('employee_commissions', function (Blueprint $table) {
                $table->string('status', 50)->default('pending')->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasColumn('commissions', 'status')) {
            Schema::table('commissions', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }

        if (Schema::hasColumn('employee_commissions', 'status')) {
            Schema::table('employee_commissions', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
