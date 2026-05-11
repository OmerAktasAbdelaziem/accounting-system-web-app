<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'invoice_number' => 'INV-' . fake()->unique()->numerify('######'),
            'customer_id' => Customer::factory(),
            'date' => fake()->dateTime(),
            'sub_total' => fake()->randomFloat(2, 100, 5000),
            'tax' => fake()->randomFloat(2, 10, 500),
            'total' => fake()->randomFloat(2, 110, 5500),
            'status' => 'draft',
        ];
    }
}
