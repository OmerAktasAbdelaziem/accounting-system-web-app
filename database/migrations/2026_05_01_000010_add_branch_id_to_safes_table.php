<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('safes', function (Blueprint $table) {
            if (!Schema::hasColumn('safes', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('location');
            }
        });
    }

    public function down(): void
    {
        Schema::table('safes', function (Blueprint $table) {
            if (Schema::hasColumn('safes', 'branch_id')) {
                $table->dropColumn('branch_id');
            }
        });
    }
};
