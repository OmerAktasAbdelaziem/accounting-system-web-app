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
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'merchant_id')) {
                $table->unsignedBigInteger('merchant_id')->nullable()->after('id');
                $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'merchant_id')) {
                $table->dropForeign(['merchant_id']);
                $table->dropColumn('merchant_id');
            }
        });
    }
};
