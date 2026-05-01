<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Branch;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create branches
        $branch1 = Branch::firstOrCreate(
            ['code' => 'MAIN'],
            ['name' => 'Main Branch', 'city' => 'Cairo', 'address' => 'Downtown Cairo']
        );
        
        $branch2 = Branch::firstOrCreate(
            ['code' => 'ALX'],
            ['name' => 'Alexandria Branch', 'city' => 'Alexandria', 'address' => 'Seafront Alexandria']
        );

        // Create customers
        $customers = [
            ['name' => 'Ahmed Trading Co.', 'email' => 'ahmed@trading.com', 'phone' => '+201001234567', 'address' => 'Cairo, Egypt', 'opening_balance' => 5000.00, 'branch_id' => $branch1->id],
            ['name' => 'Nile Supplies Ltd.', 'email' => 'info@nile.com', 'phone' => '+201112345678', 'address' => 'Alexandria, Egypt', 'opening_balance' => 3500.00, 'branch_id' => $branch2->id],
            ['name' => 'Future Technologies', 'email' => 'sales@futuretech.com', 'phone' => '+201201234567', 'address' => 'Cairo, Egypt', 'opening_balance' => 8000.00, 'branch_id' => $branch1->id],
            ['name' => 'Delta Import Export', 'email' => 'contact@deltaie.com', 'phone' => '+201009876543', 'address' => 'Giza, Egypt', 'opening_balance' => 12000.00, 'branch_id' => $branch1->id],
            ['name' => 'Mediterranean Trade', 'email' => 'hello@medtrade.com', 'phone' => '+201118765432', 'address' => 'Alexandria, Egypt', 'opening_balance' => 4200.00, 'branch_id' => $branch2->id],
        ];

        foreach ($customers as $customerData) {
            Customer::firstOrCreate(['email' => $customerData['email']], $customerData);
        }

        // Create suppliers
        $suppliers = [
            ['name' => 'Global Manufacturing', 'email' => 'procurement@global.com', 'phone' => '+2011001001', 'address' => 'Cairo, Egypt', 'opening_balance' => 15000.00, 'branch_id' => $branch1->id],
            ['name' => 'Asian Imports', 'email' => 'sales@asianmp.com', 'phone' => '+2011002002', 'address' => 'Port Said, Egypt', 'opening_balance' => 25000.00, 'branch_id' => $branch1->id],
            ['name' => 'Europe Distribution', 'email' => 'info@eurodist.com', 'phone' => '+2011003003', 'address' => 'Alexandria, Egypt', 'opening_balance' => 18000.00, 'branch_id' => $branch2->id],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::firstOrCreate(['email' => $supplierData['email']], $supplierData);
        }

        // Create invoices with line items
        $invoiceCustomers = Customer::take(3)->get();
        foreach ($invoiceCustomers as $customer) {
            $invoice = Invoice::create([
                'invoice_number' => 'INV-' . strtoupper(substr(md5(time() . $customer->id), 0, 6)),
                'customer_id' => $customer->id,
                'date' => now()->subDays(rand(1, 30)),
                'sub_total' => 0,
                'tax' => 0,
                'total' => 0,
                'status' => 'draft',
                'branch_id' => $customer->branch_id,
            ]);

            // Add line items
            for ($i = 0; $i < rand(2, 4); $i++) {
                $quantity = rand(1, 10);
                $unitPrice = rand(100, 500);
                $lineTotal = $quantity * $unitPrice;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                    'description' => 'Product Item ' . ($i + 1),
                ]);
            }

            $invoice->recalculateTotals();
        }

        // Create payroll entries
        $employees = Employee::take(5)->get();
        foreach ($employees as $employee) {
            for ($month = 1; $month <= 3; $month++) {
                $baseSalary = $employee->salary ?? 5000;
                $deductions = $baseSalary * 0.15;
                $netSalary = $baseSalary - $deductions;

                Payroll::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'month' => $month,
                        'year' => 2026,
                    ],
                    [
                        'basic_salary' => $baseSalary,
                        'allowances' => 500,
                        'deductions' => $deductions,
                        'net_salary' => $netSalary,
                        'status' => 'draft',
                    ]
                );
            }
        }

        $this->command->info('✅ Test data created successfully!');
    }
}
