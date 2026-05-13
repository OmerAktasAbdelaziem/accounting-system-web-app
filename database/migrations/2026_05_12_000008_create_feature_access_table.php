<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->string('feature_key'); // 'invoicing', 'payroll', 'inventory', etc
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            
            $table->unique(['merchant_id', 'role_id', 'feature_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_access');
    }
};
