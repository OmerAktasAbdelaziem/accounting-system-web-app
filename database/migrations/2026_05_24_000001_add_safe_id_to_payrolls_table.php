<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            if (! Schema::hasColumn('payrolls', 'safe_id')) {
                $table->foreignId('safe_id')->nullable()->after('processed_by')->constrained('safes')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            if (Schema::hasColumn('payrolls', 'safe_id')) {
                $table->dropConstrainedForeignId('safe_id');
            }
        });
    }
};