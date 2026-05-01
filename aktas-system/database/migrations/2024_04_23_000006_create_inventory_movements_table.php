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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->enum('movement_type', ['incoming', 'outgoing', 'waste', 'return', 'adjustment', 'transfer_out', 'transfer_in']);
            $table->string('movement_type_ar')->nullable(); // وارد، صادر، هالك، إرجاع، تعديل، نقل (صادر)، نقل (وارد)
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2)->nullable(); // Price at time of movement
            $table->text('reference_type')->nullable(); // 'invoice', 'purchase_order', 'manual', 'transfer', etc.
            $table->unsignedBigInteger('reference_id')->nullable(); // ID of related invoice, purchase order, etc.
            $table->text('notes')->nullable(); // Reason for movement
            $table->text('notes_ar')->nullable(); // السبب بالعربية
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->timestamps();
            $table->index(['product_id', 'movement_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
