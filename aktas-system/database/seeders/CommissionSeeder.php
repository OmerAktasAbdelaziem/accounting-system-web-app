<?php

namespace Database\Seeders;

use App\Models\Commission;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class CommissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::where('is_active', true)->limit(6)->get();

        $commissions = [
            [
                'employee_id' => $employees[0]?->id ?? 1,
                'commission_rate' => 5.00,
                'sale_amount' => 10000.00,
                'commission_amount' => 500.00,
                'commission_date' => now()->subDays(5),
                'reference_type' => 'invoice',
                'reference_id' => 1,
                'status' => 'pending',
                'notes' => 'First quarter sales commission',
            ],
            [
                'employee_id' => $employees[0]?->id ?? 1,
                'commission_rate' => 5.00,
                'sale_amount' => 8500.00,
                'commission_amount' => 425.00,
                'commission_date' => now()->subDays(3),
                'reference_type' => 'invoice',
                'reference_id' => 2,
                'status' => 'approved',
                'notes' => 'Additional sales from last week',
            ],
            [
                'employee_id' => $employees[1]?->id ?? 2,
                'commission_rate' => 3.50,
                'sale_amount' => 12000.00,
                'commission_amount' => 420.00,
                'commission_date' => now()->subDays(4),
                'reference_type' => 'invoice',
                'reference_id' => 3,
                'status' => 'pending',
                'notes' => 'Large account sales',
            ],
            [
                'employee_id' => $employees[1]?->id ?? 2,
                'commission_rate' => 3.50,
                'sale_amount' => 6500.00,
                'commission_amount' => 227.50,
                'commission_date' => now()->subDays(2),
                'reference_type' => 'invoice',
                'reference_id' => 4,
                'status' => 'paid',
                'notes' => 'Regular customer purchase',
            ],
            [
                'employee_id' => $employees[2]?->id ?? 3,
                'commission_rate' => 2.00,
                'sale_amount' => 5000.00,
                'commission_amount' => 100.00,
                'commission_date' => now()->subDays(6),
                'reference_type' => 'invoice',
                'reference_id' => 5,
                'status' => 'pending',
                'notes' => 'Routine orders',
            ],
            [
                'employee_id' => $employees[3]?->id ?? 4,
                'commission_rate' => 4.00,
                'sale_amount' => 15000.00,
                'commission_amount' => 600.00,
                'commission_date' => now()->subDays(1),
                'reference_type' => 'invoice',
                'reference_id' => 6,
                'status' => 'pending',
                'notes' => 'VIP client purchase',
            ],
            [
                'employee_id' => $employees[4]?->id ?? 5,
                'commission_rate' => 1.50,
                'sale_amount' => 7000.00,
                'commission_amount' => 105.00,
                'commission_date' => now(),
                'reference_type' => 'invoice',
                'reference_id' => 7,
                'status' => 'approved',
                'notes' => 'Today purchase',
            ],
        ];

        foreach ($commissions as $commission) {
            Commission::create($commission);
        }
    }
}
