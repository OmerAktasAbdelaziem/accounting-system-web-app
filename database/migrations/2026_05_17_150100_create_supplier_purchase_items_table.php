<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_purchase_id')->constrained('supplier_purchases')->cascadeOnDelete();
            $table->string('product_name');
            $table->decimal('weight', 15, 3);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('line_total', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_purchase_items');
    }
};
