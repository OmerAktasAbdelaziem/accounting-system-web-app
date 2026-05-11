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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key')->unique(); // e.g., 'app_name', 'currency', 'language'
            $table->longText('setting_value')->nullable(); // stores JSON or plain value
            $table->string('data_type')->default('string'); // string, integer, boolean, array, json
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('setting_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
