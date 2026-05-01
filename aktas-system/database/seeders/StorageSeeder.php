<?php

namespace Database\Seeders;

use App\Models\Storage;
use App\Models\StorageItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class StorageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $storages = [
            [
                'name' => 'Main Warehouse',
                'location' => 'Riyadh Industrial Zone - Building A',
                'description' => 'Primary warehouse for bulk storage',
                'capacity' => 10000,
                'storage_type' => 'warehouse',
                'is_active' => true,
            ],
            [
                'name' => 'Cold Storage Unit 1',
                'location' => 'Riyadh - South District',
                'description' => 'Climate controlled storage for perishables',
                'capacity' => 5000,
                'storage_type' => 'cold_storage',
                'is_active' => true,
            ],
            [
                'name' => 'Jeddah Distribution Center',
                'location' => 'Jeddah - Industrial Port Area',
                'description' => 'Secondary warehouse for rapid distribution',
                'capacity' => 8000,
                'storage_type' => 'warehouse',
                'is_active' => true,
            ],
            [
                'name' => 'Dammam Rack System',
                'location' => 'Dammam - East Industrial',
                'description' => 'Rack storage for organized product placement',
                'capacity' => 6000,
                'storage_type' => 'rack',
                'is_active' => true,
            ],
            [
                'name' => 'Regional Shelf Storage',
                'location' => 'Madinah - Central Hub',
                'description' => 'Shelf-based storage for easy access',
                'capacity' => 3000,
                'storage_type' => 'shelf',
                'is_active' => true,
            ],
        ];

        $createdStorages = [];
        foreach ($storages as $storage) {
            $createdStorages[] = Storage::create($storage);
        }

        // Get available products - get at least 8 products
        $products = Product::limit(10)->get();

        if ($products->count() < 1) {
            return; // No products, skip storage items
        }

        $storageItems = [];

        // Add items to main warehouse
        if (isset($products[0])) {
            $storageItems[] = [
                'storage_id' => $createdStorages[0]->id,
                'product_id' => $products[0]->id,
                'quantity' => 500,
                'location_code' => 'A1-1-1',
                'entry_date' => now()->subDays(10),
                'expiry_date' => now()->addMonths(6),
                'notes' => 'Bulk stock - received from supplier',
            ];
        }

        if (isset($products[1])) {
            $storageItems[] = [
                'storage_id' => $createdStorages[0]->id,
                'product_id' => $products[1]->id,
                'quantity' => 300,
                'location_code' => 'A1-1-2',
                'entry_date' => now()->subDays(8),
                'expiry_date' => now()->addMonths(8),
                'notes' => 'Main warehouse stock',
            ];
        }

        // Add items to cold storage
        if (isset($products[2])) {
            $storageItems[] = [
                'storage_id' => $createdStorages[1]->id,
                'product_id' => $products[2]->id,
                'quantity' => 200,
                'location_code' => 'C1-2-1',
                'entry_date' => now()->subDays(5),
                'expiry_date' => now()->addMonths(3),
                'notes' => 'Cold storage - must maintain temperature',
            ];
        }

        if (isset($products[3])) {
            $storageItems[] = [
                'storage_id' => $createdStorages[1]->id,
                'product_id' => $products[3]->id,
                'quantity' => 150,
                'location_code' => 'C1-2-2',
                'entry_date' => now()->subDays(4),
                'expiry_date' => now()->addMonths(2),
                'notes' => 'Perishable goods',
            ];
        }

        // Add items to Jeddah center
        if (isset($products[4])) {
            $storageItems[] = [
                'storage_id' => $createdStorages[2]->id,
                'product_id' => $products[4]->id,
                'quantity' => 400,
                'location_code' => 'J2-3-1',
                'entry_date' => now()->subDays(7),
                'expiry_date' => now()->addMonths(12),
                'notes' => 'Jeddah distribution center stock',
            ];
        }

        // Add items to Dammam rack
        if (isset($products[5])) {
            $storageItems[] = [
                'storage_id' => $createdStorages[3]->id,
                'product_id' => $products[5]->id,
                'quantity' => 350,
                'location_code' => 'R3-1-1',
                'entry_date' => now()->subDays(6),
                'expiry_date' => now()->addMonths(9),
                'notes' => 'Dammam rack - easy access',
            ];
        }

        if (isset($products[6])) {
            $storageItems[] = [
                'storage_id' => $createdStorages[3]->id,
                'product_id' => $products[6]->id,
                'quantity' => 250,
                'location_code' => 'R3-1-2',
                'entry_date' => now()->subDays(3),
                'expiry_date' => now()->addMonths(10),
                'notes' => 'Rack system storage',
            ];
        }

        // Add items to Madinah shelf
        if (isset($products[7])) {
            $storageItems[] = [
                'storage_id' => $createdStorages[4]->id,
                'product_id' => $products[7]->id,
                'quantity' => 100,
                'location_code' => 'S4-1-1',
                'entry_date' => now()->subDays(2),
                'expiry_date' => now()->addMonths(5),
                'notes' => 'Madinah shelf storage',
            ];
        }

        foreach ($storageItems as $item) {
            StorageItem::create($item);
        }
    }
}
