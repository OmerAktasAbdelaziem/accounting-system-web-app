<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('branch_access_mode')->default('inherit')->after('role_id');
            $table->json('branch_access_branch_ids')->nullable()->after('branch_access_mode');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['branch_access_mode', 'branch_access_branch_ids']);
        });
    }
};