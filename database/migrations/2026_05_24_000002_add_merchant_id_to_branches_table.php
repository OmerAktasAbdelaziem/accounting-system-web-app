<?php

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            if (!Schema::hasColumn('branches', 'merchant_id')) {
                $table->foreignId('merchant_id')->nullable()->after('id')->constrained('merchants')->nullOnDelete();
                $table->index('merchant_id');
            }
        });

        // Best-effort backfill: infer branch merchant from attached employees.
        // If a branch has mixed merchants in old data, first detected merchant is used.
        if (Schema::hasTable('branchables') && Schema::hasColumn('employees', 'merchant_id')) {
            $employeeType = addslashes(Employee::class);

            $rows = DB::table('branchables')
                ->join('employees', 'employees.id', '=', 'branchables.branchable_id')
                ->where('branchables.branchable_type', $employeeType)
                ->whereNotNull('employees.merchant_id')
                ->select('branchables.branch_id', DB::raw('MIN(employees.merchant_id) as merchant_id'))
                ->groupBy('branchables.branch_id')
                ->get();

            foreach ($rows as $row) {
                DB::table('branches')
                    ->where('id', $row->branch_id)
                    ->whereNull('merchant_id')
                    ->update(['merchant_id' => $row->merchant_id]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            if (Schema::hasColumn('branches', 'merchant_id')) {
                $table->dropForeign(['merchant_id']);
                $table->dropIndex(['merchant_id']);
                $table->dropColumn('merchant_id');
            }
        });
    }
};
