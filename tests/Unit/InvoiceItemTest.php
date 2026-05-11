<?php

namespace Tests\Unit;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_can_add_line_items(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['selling_price' => 100.00]);
        $invoice = Invoice::factory()->create(['customer_id' => $customer->id]);

        $invoice->addItem($product->id, 5, 100.00, 'Test item');

        $this->assertCount(1, $invoice->items);
        $this->assertEquals(5, $invoice->items->first()->quantity);
        $this->assertEquals(500.00, $invoice->items->first()->line_total);
    }

    public function test_invoice_recalculates_totals(): void
    {
        $customer = Customer::factory()->create();
        $invoice = Invoice::factory()->create(['customer_id' => $customer->id]);

        $invoice->addItem(null, 2, 100.00);
        $invoice->addItem(null, 3, 50.00);
        $invoice->recalculateTotals();

        $this->assertEquals(350.00, $invoice->sub_total); // (2*100) + (3*50)
        $this->assertEquals(52.50, $invoice->tax); // 350 * 0.15
        $this->assertEquals(402.50, $invoice->total); // 350 + 52.50
    }
}
