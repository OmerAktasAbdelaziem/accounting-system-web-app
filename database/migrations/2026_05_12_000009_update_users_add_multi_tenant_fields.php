<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add merchant relationship (null for super admin)
            if (!Schema::hasColumn('users', 'merchant_id')) {
                $table->foreignId('merchant_id')->nullable()->after('id')->constrained('merchants')->nullOnDelete();
            }
            
            // Add user type (super_admin, merchant_admin, employee, etc)
            if (!Schema::hasColumn('users', 'user_type')) {
                $table->string('user_type')->default('employee')->after('merchant_id');
            }
            
            // Track active subscription
            if (!Schema::hasColumn('users', 'subscription_id')) {
                $table->foreignId('subscription_id')->nullable()->after('user_type')->constrained('subscriptions')->nullOnDelete();
            }

            // Add default language
            if (!Schema::hasColumn('users', 'default_language')) {
                $table->string('default_language')->default('en')->after('subscription_id');
            }

            // Add default currency
            if (!Schema::hasColumn('users', 'default_currency_id')) {
                $table->foreignId('default_currency_id')->nullable()->after('default_language')->constrained('currencies')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'merchant_id')) {
                $table->dropForeignKeyIfExists(['merchant_id']);
                $table->dropColumn('merchant_id');
            }
            if (Schema::hasColumn('users', 'user_type')) {
                $table->dropColumn('user_type');
            }
            if (Schema::hasColumn('users', 'subscription_id')) {
                $table->dropForeignKeyIfExists(['subscription_id']);
                $table->dropColumn('subscription_id');
            }
            if (Schema::hasColumn('users', 'default_language')) {
                $table->dropColumn('default_language');
            }
            if (Schema::hasColumn('users', 'default_currency_id')) {
                $table->dropForeignKeyIfExists(['default_currency_id']);
                $table->dropColumn('default_currency_id');
            }
        });
    }
};
