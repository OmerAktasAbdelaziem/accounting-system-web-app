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
        Schema::table('users', function (Blueprint $table) {
            // Add API token column for token-based authentication
            if (!Schema::hasColumn('users', 'api_token')) {
                $table->string('api_token', 80)->unique()->nullable()->after('remember_token');
            }

            // Add last login tracking
            if (!Schema::hasColumn('users', 'last_login')) {
                $table->timestamp('last_login')->nullable()->after('api_token');
            }

            // Add index for faster token lookups
            if (!Schema::hasColumn('users', 'api_token_index_added')) {
                $table->index('api_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'api_token')) {
                $table->dropUnique(['api_token']);
                $table->dropIndex(['api_token']);
                $table->dropColumn('api_token');
            }

            if (Schema::hasColumn('users', 'last_login')) {
                $table->dropColumn('last_login');
            }
        });
    }
};
