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
            // Add token expiration timestamp
            $table->timestamp('api_token_expires_at')->nullable()->after('api_token');
            
            // Add index for faster expiration checks
            $table->index('api_token_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['api_token_expires_at']);
            $table->dropColumn('api_token_expires_at');
        });
    }
};
