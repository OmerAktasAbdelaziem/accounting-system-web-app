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
        if (Schema::hasColumn('safe_currencies', 'exchange_rate')) {
            Schema::table('safe_currencies', function (Blueprint $table) {
                $table->dropColumn('exchange_rate');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('safe_currencies', 'exchange_rate')) {
            Schema::table('safe_currencies', function (Blueprint $table) {
                $table->decimal('exchange_rate', 10, 4)->default(1);
            });
        }
    }
};
