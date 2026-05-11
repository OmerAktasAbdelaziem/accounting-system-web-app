<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('storages', function (Blueprint $table) {
            if (!Schema::hasColumn('storages', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('storages', function (Blueprint $table) {
            if (Schema::hasColumn('storages', 'branch_id')) {
                $table->dropColumn('branch_id');
            }
        });
    }
};
