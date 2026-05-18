<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('storage_items', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('storage_id');
            $table->decimal('weight', 12, 2)->nullable()->after('quantity');
            $table->decimal('unit_price', 12, 2)->nullable()->after('weight');
            $table->decimal('total_price', 12, 2)->nullable()->after('unit_price');
        });

        Schema::table('storage_transfers', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('to_storage_id');
            $table->decimal('weight', 12, 2)->nullable()->after('quantity');
            $table->decimal('unit_price', 12, 2)->nullable()->after('weight');
            $table->decimal('total_price', 12, 2)->nullable()->after('unit_price');
        });

        foreach (DB::table('storage_items')
            ->leftJoin('products', 'storage_items.product_id', '=', 'products.id')
            ->select('storage_items.id', 'storage_items.quantity', 'products.name as product_name', 'products.purchase_price as unit_price')
            ->get() as $row) {
            DB::table('storage_items')
                ->where('id', $row->id)
                ->update([
                    'product_name' => $row->product_name,
                    'weight' => 0,
                    'unit_price' => $row->unit_price ?? 0,
                    'total_price' => ((float) $row->quantity) * ((float) ($row->unit_price ?? 0)),
                ]);
        }

        foreach (DB::table('storage_transfers')
            ->leftJoin('products', 'storage_transfers.product_id', '=', 'products.id')
            ->select('storage_transfers.id', 'storage_transfers.quantity', 'products.name as product_name', 'products.purchase_price as unit_price')
            ->get() as $row) {
            DB::table('storage_transfers')
                ->where('id', $row->id)
                ->update([
                    'product_name' => $row->product_name,
                    'weight' => 0,
                    'unit_price' => $row->unit_price ?? 0,
                    'total_price' => ((float) $row->quantity) * ((float) ($row->unit_price ?? 0)),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('storage_items', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'weight', 'unit_price', 'total_price']);
        });

        Schema::table('storage_transfers', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'weight', 'unit_price', 'total_price']);
        });
    }
};