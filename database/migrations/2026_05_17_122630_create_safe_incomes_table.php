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
        Schema::create('safe_incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('safe_id')->constrained('safes')->cascadeOnDelete();
            $table->decimal('amount', 18, 2);
            $table->enum('source', ['cash', 'bank']); // Where the income came from
            $table->foreignId('currency_id')->nullable()->constrained('safe_currencies')->cascadeOnDelete();
            $table->string('reference')->nullable(); // Invoice reference, check number, etc
            $table->text('notes')->nullable();
            $table->timestamps();
                $table->softDeletes();
                $table->index('safe_id');
                $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safe_incomes');
    }
};
