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
        Schema::create('storage_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_storage_id')->constrained('storages')->cascadeOnDelete();
            $table->foreignId('to_storage_id')->constrained('storages')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity');
            $table->text('description')->nullable();
            $table->dateTime('transfer_date')->useCurrent();
            $table->foreignId('transferred_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['from_storage_id', 'to_storage_id']);
            $table->index('transfer_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_transfers');
    }
};
