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
        // Chart of Accounts (دليل الحسابات)
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_code', 20)->unique(); // e.g., 1000, 2000, 3000
            $table->string('account_name');
            $table->string('account_name_ar'); // Arabic account name
            $table->enum('account_type', ['asset', 'liability', 'equity', 'revenue', 'expense']); // Assets, Liabilities, Equity, Revenue, Expenses
            $table->foreignId('parent_account_id')->nullable()->constrained('chart_of_accounts')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0); // Opening balance
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
