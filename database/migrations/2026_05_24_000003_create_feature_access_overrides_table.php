<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_access_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('feature_key');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->unique(['merchant_id', 'user_id', 'feature_key'], 'feature_access_overrides_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_access_overrides');
    }
};
