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
        Schema::table('invoices', function (Blueprint $table) {
            $table->after('tax', function (Blueprint $table) {
                $table->decimal('vat_rate', 5, 2)->default(0)->comment('VAT Rate as percentage');
                $table->decimal('vat_amount', 10, 2)->default(0)->comment('Calculated VAT amount');
            });
            
            // Add merchant_id to link to merchant for VAT lookup
            $table->foreignId('merchant_id')->nullable()->constrained('merchants')->onDelete('cascade')->comment('Associated merchant for multi-tenant support');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['vat_rate', 'vat_amount', 'merchant_id']);
        });
    }
};
