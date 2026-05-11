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
        Schema::create('storages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Warehouse/Storage name
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->decimal('capacity', 12, 2)->nullable();
            $table->decimal('current_usage', 12, 2)->default(0);
            $table->string('storage_type')->default('warehouse'); // warehouse, cold_storage, etc
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('storage_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storages');
    }
};
