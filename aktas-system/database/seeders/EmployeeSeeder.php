<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            [
                'employee_code' => 'EMP-001',
                'name' => 'Ahmed Hassan',
                'name_ar' => 'أحمد حسن',
                'email' => 'ahmed.hassan@hamidltd.com',
                'phone' => '+966501234567',
                'position' => 'Senior Sales Manager',
                'position_ar' => 'مدير المبيعات الأول',
                'address' => 'Riyadh, Saudi Arabia',
                'address_ar' => 'الرياض، المملكة العربية السعودية',
                'hire_date' => '2022-01-15',
                'base_salary' => 8000.00,
                'commission_rate' => 5.00, // 5% commission
                'commission_type' => 'percentage',
                'department' => 'sales',
                'is_active' => true,
            ],
            [
                'employee_code' => 'EMP-002',
                'name' => 'Fatima Al-Rashid',
                'name_ar' => 'فاطمة الرشيد',
                'email' => 'fatima.rashid@hamidltd.com',
                'phone' => '+966502345678',
                'position' => 'Sales Executive',
                'position_ar' => 'مسؤول المبيعات',
                'address' => 'Jeddah, Saudi Arabia',
                'address_ar' => 'جدة، المملكة العربية السعودية',
                'hire_date' => '2022-03-20',
                'base_salary' => 5500.00,
                'commission_rate' => 3.50, // 3.5% commission
                'commission_type' => 'percentage',
                'department' => 'sales',
                'is_active' => true,
            ],
            [
                'employee_code' => 'EMP-003',
                'name' => 'Mohammed Ibrahim',
                'name_ar' => 'محمد إبراهيم',
                'email' => 'mohammed.ibrahim@hamidltd.com',
                'phone' => '+966503456789',
                'position' => 'Inventory Manager',
                'position_ar' => 'مدير المخزون',
                'address' => 'Dammam, Saudi Arabia',
                'address_ar' => 'الدمام، المملكة العربية السعودية',
                'hire_date' => '2021-06-10',
                'base_salary' => 6500.00,
                'commission_rate' => 2.00, // 2% commission
                'commission_type' => 'percentage',
                'department' => 'inventory',
                'is_active' => true,
            ],
            [
                'employee_code' => 'EMP-004',
                'name' => 'Noor Al-Dosari',
                'name_ar' => 'نور الدوسري',
                'email' => 'noor.dosari@hamidltd.com',
                'phone' => '+966504567890',
                'position' => 'Junior Sales Associate',
                'position_ar' => 'مساعد مبيعات',
                'address' => 'Riyadh, Saudi Arabia',
                'address_ar' => 'الرياض، المملكة العربية السعودية',
                'hire_date' => '2023-01-01',
                'base_salary' => 3500.00,
                'commission_rate' => 4.00, // 4% commission
                'commission_type' => 'percentage',
                'department' => 'sales',
                'is_active' => true,
            ],
            [
                'employee_code' => 'EMP-005',
                'name' => 'Sarah Al-Qahtani',
                'name_ar' => 'سارة القحطاني',
                'email' => 'sarah.qahtani@hamidltd.com',
                'phone' => '+966505678901',
                'position' => 'Chief Accountant',
                'position_ar' => 'رئيس المحاسبين',
                'address' => 'Riyadh, Saudi Arabia',
                'address_ar' => 'الرياض، المملكة العربية السعودية',
                'hire_date' => '2020-05-15',
                'base_salary' => 9000.00,
                'commission_rate' => 0.00, // No commission
                'commission_type' => 'fixed',
                'department' => 'accounting',
                'is_active' => true,
            ],
            [
                'employee_code' => 'EMP-006',
                'name' => 'Khaled Al-Otaibi',
                'name_ar' => 'خالد العتيبي',
                'email' => 'khaled.otaibi@hamidltd.com',
                'phone' => '+966506789012',
                'position' => 'Warehouse Supervisor',
                'position_ar' => 'مشرف المستودع',
                'address' => 'Riyadh, Saudi Arabia',
                'address_ar' => 'الرياض، المملكة العربية السعودية',
                'hire_date' => '2021-09-01',
                'base_salary' => 4500.00,
                'commission_rate' => 1.50, // 1.5% commission
                'commission_type' => 'percentage',
                'department' => 'inventory',
                'is_active' => true,
            ],
            [
                'employee_code' => 'EMP-007',
                'name' => 'Layla Al-Shammary',
                'name_ar' => 'ليلى الشمري',
                'email' => 'layla.shammary@hamidltd.com',
                'phone' => '+966507890123',
                'position' => 'Sales Executive',
                'position_ar' => 'مسؤول المبيعات',
                'address' => 'Khobar, Saudi Arabia',
                'address_ar' => 'الخبر، المملكة العربية السعودية',
                'hire_date' => '2022-08-15',
                'base_salary' => 5200.00,
                'commission_rate' => 3.75, // 3.75% commission
                'commission_type' => 'percentage',
                'department' => 'sales',
                'is_active' => true,
            ],
            [
                'employee_code' => 'EMP-008',
                'name' => 'Zainab Al-Zahrani',
                'name_ar' => 'زينب الزهراني',
                'email' => 'zainab.zahrani@hamidltd.com',
                'phone' => '+966508901234',
                'position' => 'Accountant',
                'position_ar' => 'محاسب',
                'address' => 'Jeddah, Saudi Arabia',
                'address_ar' => 'جدة، المملكة العربية السعودية',
                'hire_date' => '2021-11-01',
                'base_salary' => 6000.00,
                'commission_rate' => 0.00, // No commission
                'commission_type' => 'fixed',
                'department' => 'accounting',
                'is_active' => true,
            ],
            [
                'employee_code' => 'EMP-009',
                'name' => 'Omar Al-Sudairi',
                'name_ar' => 'عمر السديري',
                'email' => 'omar.sudairi@hamidltd.com',
                'phone' => '+966509012345',
                'position' => 'General Manager',
                'position_ar' => 'المدير العام',
                'address' => 'Riyadh, Saudi Arabia',
                'address_ar' => 'الرياض، المملكة العربية السعودية',
                'hire_date' => '2019-01-01',
                'base_salary' => 15000.00,
                'commission_rate' => 0.00, // No commission
                'commission_type' => 'fixed',
                'department' => 'management',
                'is_active' => true,
            ],
            [
                'employee_code' => 'EMP-010',
                'name' => 'Rayan Al-Harbi',
                'name_ar' => 'ريان الحربي',
                'email' => 'rayan.harbi@hamidltd.com',
                'phone' => '+966510123456',
                'position' => 'Senior Sales Manager',
                'position_ar' => 'مدير المبيعات الأول',
                'address' => 'Riyadh, Saudi Arabia',
                'address_ar' => 'الرياض، المملكة العربية السعودية',
                'hire_date' => '2020-03-15',
                'base_salary' => 8500.00,
                'commission_rate' => 5.50, // 5.5% commission
                'commission_type' => 'percentage',
                'department' => 'sales',
                'is_active' => true,
            ],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}
