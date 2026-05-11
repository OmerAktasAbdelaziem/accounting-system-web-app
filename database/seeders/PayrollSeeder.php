<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Database\Seeder;

class PayrollSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();

        foreach ($employees as $employee) {
            Payroll::create([
                'employee_id' => $employee->id,
                'year' => now()->year,
                'month' => now()->month,
                'basic_salary' => 8000,
                'allowances' => 1000,
                'deductions' => 500,
                'net_salary' => 8500,
                'status' => 'draft',
                'notes' => 'Seeded payroll record',
            ]);
        }
    }
}
