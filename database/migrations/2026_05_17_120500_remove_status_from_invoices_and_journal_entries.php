<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'status')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }

        if (Schema::hasTable('journal_entries') && Schema::hasColumn('journal_entries', 'status')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                $table->dropIndex('journal_entries_date_status_index');
            });

            Schema::table('journal_entries', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('invoices') && !Schema::hasColumn('invoices', 'status')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->string('status')->default('draft');
            });
        }

        if (Schema::hasTable('journal_entries') && !Schema::hasColumn('journal_entries', 'status')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                $table->enum('status', ['draft', 'posted', 'reversed'])->default('draft');
                $table->index(['date', 'status']);
            });
        }
    }
};