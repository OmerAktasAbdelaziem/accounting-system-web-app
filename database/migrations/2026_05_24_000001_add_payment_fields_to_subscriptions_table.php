<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'payment_method')) {
                $table->string('payment_method', 50)->nullable()->after('is_active');
            }

            if (!Schema::hasColumn('subscriptions', 'amount_paid')) {
                $table->decimal('amount_paid', 12, 2)->nullable()->after('payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'amount_paid')) {
                $table->dropColumn('amount_paid');
            }

            if (Schema::hasColumn('subscriptions', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });
    }
};