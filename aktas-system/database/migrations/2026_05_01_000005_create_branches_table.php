<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('manager_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add branch_id to tables that support branch scoping
        if (!Schema::hasColumn('safes', 'branch_id')) {
            Schema::table('safes', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('id');
            });
        }

        if (!Schema::hasColumn('storages', 'branch_id')) {
            Schema::table('storages', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('safes', 'branch_id')) {
            Schema::table('safes', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasColumn('storages', 'branch_id')) {
            Schema::table('storages', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }

        Schema::dropIfExists('branches');
    }
};
