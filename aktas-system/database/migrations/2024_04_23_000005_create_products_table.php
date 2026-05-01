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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Product name
            $table->string('name_ar')->nullable(); // اسم المنتج بالعربية
            $table->string('sku')->unique()->nullable(); // Stock Keeping Unit
            $table->string('barcode')->unique()->nullable(); // Product barcode
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->text('description')->nullable();
            $table->string('unit')->default('piece'); // piece, kg, liter, etc.
            $table->string('unit_ar')->nullable(); // قطعة، كيلوجرام، لتر
            $table->decimal('purchase_price', 12, 2)->default(0); // Cost price
            $table->decimal('selling_price', 12, 2)->default(0); // Retail price
            $table->decimal('wholesale_price', 12, 2)->nullable(); // Wholesale price (if applicable)
            $table->decimal('profit_margin', 5, 2)->nullable(); // % Profit margin
            $table->integer('min_stock')->default(10); // Minimum stock level for alert
            $table->integer('current_stock')->default(0); // Current stock quantity
            $table->boolean('is_active')->default(true);
            $table->boolean('track_inventory')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['is_active', 'category_id']);

            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->fullText(['name', 'name_ar', 'sku']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
