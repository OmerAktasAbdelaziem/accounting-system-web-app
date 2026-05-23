<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchant_currencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->unique(['merchant_id', 'currency_id']);
        });
    }

    public function down(): void
    {
            if (Schema::hasTable('merchant_currencies')) {
                return;
            }
        Schema::dropIfExists('merchant_currencies');
    }
};
