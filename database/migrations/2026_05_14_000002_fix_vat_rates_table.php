<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vat_rates', function (Blueprint $table) {
            // Add missing columns that the model expects
            if (!Schema::hasColumn('vat_rates', 'rate')) {
                $table->decimal('rate', 5, 2)->nullable()->after('rate_percentage');
            }
            
            if (!Schema::hasColumn('vat_rates', 'is_enabled')) {
                $table->boolean('is_enabled')->default(true)->after('is_active');
            }
            
            if (!Schema::hasColumn('vat_rates', 'description')) {
                $table->string('description')->nullable()->after('is_enabled');
            }
            
            // Rename old columns if new ones don't exist yet
            if (Schema::hasColumn('vat_rates', 'rate_percentage') && Schema::hasColumn('vat_rates', 'rate')) {
                // Copy data from rate_percentage to rate if rate is empty
                \DB::table('vat_rates')->whereNull('rate')->update([
                    'rate' => \DB::raw('rate_percentage')
                ]);
            }
            
            if (Schema::hasColumn('vat_rates', 'is_active') && Schema::hasColumn('vat_rates', 'is_enabled')) {
                // Copy data from is_active to is_enabled if is_enabled is not matching
                \DB::table('vat_rates')->update([
                    'is_enabled' => \DB::raw('is_active')
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('vat_rates', function (Blueprint $table) {
            if (Schema::hasColumn('vat_rates', 'rate')) {
                $table->dropColumn('rate');
            }
            if (Schema::hasColumn('vat_rates', 'is_enabled')) {
                $table->dropColumn('is_enabled');
            }
            if (Schema::hasColumn('vat_rates', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
