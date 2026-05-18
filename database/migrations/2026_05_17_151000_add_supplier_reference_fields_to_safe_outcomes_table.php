<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('safe_outcomes', function (Blueprint $table) {
            $table->string('reference_type')->nullable()->after('currency_id');
            $table->foreignId('supplier_id')->nullable()->after('reference_type')->constrained('suppliers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('safe_outcomes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supplier_id');
            $table->dropColumn('reference_type');
        });
    }
};
