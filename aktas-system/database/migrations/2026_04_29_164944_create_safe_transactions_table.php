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
        Schema::create('safe_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('safe_id');
            $table->string('type'); // deposit, withdrawal, transfer
            $table->decimal('amount', 15, 2);
            $table->string('reference_type')->nullable(); // invoice, sales, commission, etc
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('safe_id')->references('id')->on('safes')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->index('type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('safe_transactions');
    }
};
