<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            // Add super_admin_id if it doesn't exist
            if (!Schema::hasColumn('merchants', 'super_admin_id')) {
                $table->foreignId('super_admin_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }

            // Add business_name if it doesn't exist
            if (!Schema::hasColumn('merchants', 'business_name')) {
                $table->string('business_name')->nullable()->after('name');
            }

            // Add slug if it doesn't exist
            if (!Schema::hasColumn('merchants', 'slug')) {
                $table->string('slug')->unique()->nullable()->after('business_name');
            }

            // Add description if it doesn't exist
            if (!Schema::hasColumn('merchants', 'description')) {
                $table->text('description')->nullable()->after('slug');
            }

            // Add subscription_id if it doesn't exist (FK to subscriptions)
            if (!Schema::hasColumn('merchants', 'subscription_id')) {
                $table->foreignId('subscription_id')->nullable()->after('description')->constrained('subscriptions')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            if (Schema::hasColumn('merchants', 'super_admin_id')) {
                $table->dropForeignKeyIfExists(['super_admin_id']);
                $table->dropColumn('super_admin_id');
            }
            if (Schema::hasColumn('merchants', 'business_name')) {
                $table->dropColumn('business_name');
            }
            if (Schema::hasColumn('merchants', 'slug')) {
                $table->dropColumn('slug');
            }
            if (Schema::hasColumn('merchants', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('merchants', 'subscription_id')) {
                $table->dropForeignKeyIfExists(['subscription_id']);
                $table->dropColumn('subscription_id');
            }
        });
    }
};
