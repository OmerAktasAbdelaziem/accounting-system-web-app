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
        // Warehouses (المستودعات)
        if (!Schema::hasTable('warehouses')) {
            Schema::create('warehouses', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('name_ar'); // Arabic warehouse name
                $table->string('location')->nullable();
                $table->string('location_ar')->nullable(); // Arabic location
                $table->text('description')->nullable();
                $table->integer('capacity')->nullable(); // Storage capacity
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // Warehouse Inventory (مخزون المستودع)
        if (!Schema::hasTable('warehouse_inventory')) {
            Schema::create('warehouse_inventory', function (Blueprint $table) {
                $table->id();
                $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->integer('quantity')->default(0)->index();
                $table->integer('reserved_quantity')->default(0); // Reserved for pending orders
                $table->timestamp('last_updated_at')->useCurrent();
                $table->timestamps();
                
                $table->unique(['warehouse_id', 'product_id']);
                $table->index(['warehouse_id', 'quantity']);
            });
        }

        // Warehouse Transfers (نقل بين المستودعات)
        if (!Schema::hasTable('warehouse_transfers')) {
            Schema::create('warehouse_transfers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
                $table->foreignId('from_warehouse_id')->constrained('warehouses')->onDelete('restrict');
                $table->foreignId('to_warehouse_id')->constrained('warehouses')->onDelete('restrict');
                $table->integer('quantity')->index();
                $table->date('transfer_date')->index();
                $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
                $table->enum('status', ['pending', 'in_transit', 'received', 'rejected'])->default('pending')->index();
                $table->string('reference_number')->nullable()->unique(); // e.g., TRF-001
                $table->text('notes')->nullable();
                $table->text('notes_ar')->nullable(); // Arabic notes
                $table->timestamps();
                $table->softDeletes();
                
                $table->index(['from_warehouse_id', 'to_warehouse_id', 'transfer_date'], 'wh_transfer_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_transfers');
        Schema::dropIfExists('warehouse_inventory');
        Schema::dropIfExists('warehouses');
    }
};
