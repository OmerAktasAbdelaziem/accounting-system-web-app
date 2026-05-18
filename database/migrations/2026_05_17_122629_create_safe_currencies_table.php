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
        Schema::create('safe_currencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('safe_id')->constrained('safes')->cascadeOnDelete();
            $table->string('code')->unique(); // USD, EUR, GBP, etc
            $table->string('name'); // Dollar, Euro, British Pound, etc
            $table->decimal('balance', 18, 2)->default(0); // Balance in this currency
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['safe_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safe_currencies');
    }
};
