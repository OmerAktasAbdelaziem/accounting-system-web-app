<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_branch_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->unique(['merchant_id', 'role_id', 'branch_id'], 'role_branch_accesses_unique');
            $table->index(['merchant_id', 'role_id', 'is_enabled'], 'role_branch_accesses_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_branch_accesses');
    }
};