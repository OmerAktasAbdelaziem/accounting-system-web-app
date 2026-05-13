<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('admin_email');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->foreignId('default_currency_id')->constrained('currencies');
            $table->integer('max_currencies')->default(5);
            $table->integer('max_languages')->default(3);
            $table->string('default_language')->default('en');
            $table->integer('max_employees')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('subscription_expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
