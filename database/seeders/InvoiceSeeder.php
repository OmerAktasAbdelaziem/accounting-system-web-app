<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();

        foreach ($customers as $customer) {
            Invoice::create([
                'invoice_number' => strtoupper('INV-' . Str::random(6)),
                'customer_id' => $customer->id,
                'date' => now()->subDays(30),
                'sub_total' => 10000,
                'tax' => 1500,
                'total' => 11500,
                'status' => 'draft',
            ]);

            Invoice::create([
                'invoice_number' => strtoupper('INV-' . Str::random(6)),
                'customer_id' => $customer->id,
                'date' => now()->subDays(15),
                'sub_total' => 25000,
                'tax' => 3750,
                'total' => 28750,
                'status' => 'draft',
            ]);
        }
    }
}
