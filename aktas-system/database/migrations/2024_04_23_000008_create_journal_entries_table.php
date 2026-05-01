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
        // Journal Entries (قيود اليومية)
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index();
            $table->string('description');
            $table->string('description_ar')->nullable(); // Arabic description
            $table->string('reference_number')->nullable()->unique(); // e.g., INV-001, PO-001
            $table->string('reference_type')->nullable(); // e.g., invoice, bill, payroll
            $table->foreignId('reference_id')->nullable(); // Links to invoices, bills, etc.
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->decimal('total_debit', 15, 2)->default(0);
            $table->decimal('total_credit', 15, 2)->default(0);
            $table->enum('status', ['draft', 'posted', 'reversed'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['date', 'status']);
        });

        // Journal Entry Items (تفاصيل القيود)
        Schema::create('journal_entry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('chart_of_accounts')->onDelete('restrict');
            $table->decimal('debit', 15, 2)->default(0)->index();
            $table->decimal('credit', 15, 2)->default(0)->index();
            $table->string('description')->nullable();
            $table->string('description_ar')->nullable(); // Arabic description
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['journal_entry_id', 'account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entry_items');
        Schema::dropIfExists('journal_entries');
    }
};
