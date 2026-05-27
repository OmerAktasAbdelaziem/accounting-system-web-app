<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Roles
        $adminRole = Role::updateOrCreate([
            'name' => 'Admin',
        ], [
            'name' => 'Admin',
            'name_ar' => 'مسؤول النظام',
            'description' => 'Full system access',
        ]);

        $branchManagerRole = Role::updateOrCreate([
            'name' => 'Branch Manager',
        ], [
            'name' => 'Branch Manager',
            'name_ar' => 'مدير الفرع',
            'description' => 'Manage branch operations',
        ]);

        $accountantRole = Role::updateOrCreate([
            'name' => 'Accountant',
        ], [
            'name' => 'Accountant',
            'name_ar' => 'محاسب',
            'description' => 'Handle accounting operations',
        ]);

        $cashierRole = Role::updateOrCreate([
            'name' => 'Cashier',
        ], [
            'name' => 'Cashier',
            'name_ar' => 'أمين الصندوق',
            'description' => 'Handle cash transactions',
        ]);

        $viewOnlyRole = Role::updateOrCreate([
            'name' => 'View-Only',
        ], [
            'name' => 'View-Only',
            'name_ar' => 'عرض فقط',
            'description' => 'Can only view data',
        ]);

        // Create Permissions
        $permissions = [
            // Product Management
            ['name' => 'create_product', 'name_ar' => 'إنشاء منتج', 'description' => 'Can create new products'],
            ['name' => 'edit_product', 'name_ar' => 'تعديل منتج', 'description' => 'Can edit products'],
            ['name' => 'delete_product', 'name_ar' => 'حذف منتج', 'description' => 'Can delete products'],
            ['name' => 'view_product', 'name_ar' => 'عرض منتجات', 'description' => 'Can view products'],

            // Category Management
            ['name' => 'create_category', 'name_ar' => 'إنشاء فئة', 'description' => 'Can create categories'],
            ['name' => 'edit_category', 'name_ar' => 'تعديل فئة', 'description' => 'Can edit categories'],
            ['name' => 'delete_category', 'name_ar' => 'حذف فئة', 'description' => 'Can delete categories'],
            ['name' => 'view_category', 'name_ar' => 'عرض فئات', 'description' => 'Can view categories'],

            // Inventory Management
            ['name' => 'record_inventory_movement', 'name_ar' => 'تسجيل حركة مخزون', 'description' => 'Can record inventory movements'],
            ['name' => 'view_inventory', 'name_ar' => 'عرض المخزون', 'description' => 'Can view inventory'],
            ['name' => 'edit_inventory', 'name_ar' => 'تعديل المخزون', 'description' => 'Can edit inventory'],

            // Reporting
            ['name' => 'view_reports', 'name_ar' => 'عرض التقارير', 'description' => 'Can view reports'],
            ['name' => 'export_reports', 'name_ar' => 'تصدير التقارير', 'description' => 'Can export reports'],

            // Sales Management
            ['name' => 'view_sales', 'name_ar' => 'عرض المبيعات', 'description' => 'Can view the sales page and records', 'category' => 'Sales Management'],

            // Safe Management
            ['name' => 'view_safe', 'name_ar' => 'عرض الخزنة', 'description' => 'Can view safes', 'category' => 'Safe Management'],
            ['name' => 'create_safe', 'name_ar' => 'إنشاء خزنة', 'description' => 'Can create safes', 'category' => 'Safe Management'],
            ['name' => 'edit_safe', 'name_ar' => 'تعديل خزنة', 'description' => 'Can edit safes', 'category' => 'Safe Management'],
            ['name' => 'delete_safe', 'name_ar' => 'حذف خزنة', 'description' => 'Can delete safes', 'category' => 'Safe Management'],
            ['name' => 'deposit_safe', 'name_ar' => 'إيداع الخزنة', 'description' => 'Can deposit into safes', 'category' => 'Safe Management'],
            ['name' => 'withdraw_safe', 'name_ar' => 'سحب من الخزنة', 'description' => 'Can withdraw from safes', 'category' => 'Safe Management'],

            // Customer Management
            ['name' => 'create_customer', 'name_ar' => 'إنشاء عميل', 'description' => 'Can create customers'],
            ['name' => 'edit_customer', 'name_ar' => 'تعديل عميل', 'description' => 'Can edit customers'],
            ['name' => 'delete_customer', 'name_ar' => 'حذف عميل', 'description' => 'Can delete customers'],
            ['name' => 'view_customer', 'name_ar' => 'عرض العملاء', 'description' => 'Can view customers'],

            // Supplier Management
            ['name' => 'create_supplier', 'name_ar' => 'إنشاء مورد', 'description' => 'Can create suppliers'],
            ['name' => 'edit_supplier', 'name_ar' => 'تعديل مورد', 'description' => 'Can edit suppliers'],
            ['name' => 'delete_supplier', 'name_ar' => 'حذف مورد', 'description' => 'Can delete suppliers'],
            ['name' => 'view_supplier', 'name_ar' => 'عرض الموردين', 'description' => 'Can view suppliers'],

            // Invoice Management
            ['name' => 'create_invoice', 'name_ar' => 'إنشاء فاتورة', 'description' => 'Can create invoices'],
            ['name' => 'edit_invoice', 'name_ar' => 'تعديل فاتورة', 'description' => 'Can edit invoices'],
            ['name' => 'delete_invoice', 'name_ar' => 'حذف فاتورة', 'description' => 'Can delete invoices'],
            ['name' => 'view_invoice', 'name_ar' => 'عرض الفواتير', 'description' => 'Can view invoices'],
            ['name' => 'export_invoice_pdf', 'name_ar' => 'تصدير الفاتورة PDF', 'description' => 'Can export invoice PDF'],

            // Payroll Management
            ['name' => 'create_payroll', 'name_ar' => 'إنشاء راتب', 'description' => 'Can create payroll records'],
            ['name' => 'edit_payroll', 'name_ar' => 'تعديل راتب', 'description' => 'Can edit payroll records'],
            ['name' => 'delete_payroll', 'name_ar' => 'حذف راتب', 'description' => 'Can delete payroll records'],
            ['name' => 'view_payroll', 'name_ar' => 'عرض الرواتب', 'description' => 'Can view payroll records'],
            ['name' => 'process_payroll', 'name_ar' => 'معالجة الرواتب', 'description' => 'Can process payroll'],

            // Branch Management
            ['name' => 'create_branch', 'name_ar' => 'إنشاء فرع', 'description' => 'Can create branches'],
            ['name' => 'edit_branch', 'name_ar' => 'تعديل فرع', 'description' => 'Can edit branches'],
            ['name' => 'delete_branch', 'name_ar' => 'حذف فرع', 'description' => 'Can delete branches'],
            ['name' => 'view_branch', 'name_ar' => 'عرض الفروع', 'description' => 'Can view branches'],
            ['name' => 'view_branch_reports', 'name_ar' => 'عرض تقارير الفروع', 'description' => 'Can view branch reports'],

            // User Management
            ['name' => 'create_user', 'name_ar' => 'إنشاء مستخدم', 'description' => 'Can create users'],
            ['name' => 'edit_user', 'name_ar' => 'تعديل مستخدم', 'description' => 'Can edit users'],
            ['name' => 'delete_user', 'name_ar' => 'حذف مستخدم', 'description' => 'Can delete users'],
            ['name' => 'view_user', 'name_ar' => 'عرض مستخدمين', 'description' => 'Can view users'],

            // Settings
            ['name' => 'manage_settings', 'name_ar' => 'إدارة الإعدادات', 'description' => 'Can manage system settings'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name']],
                $perm
            );
        }

        // Get all permissions
        $allPermissions = Permission::all();
        $allPermissionIds = $allPermissions->pluck('id')->toArray();

        // Assign permissions to roles
        // Admin - All permissions
        $adminRole->permissions()->syncWithoutDetaching($allPermissionIds);

        // Branch Manager - Most permissions except user management and settings
        $branchManagerPermissions = $allPermissions
            ->whereNotIn('name', ['create_user', 'edit_user', 'delete_user', 'manage_settings'])
            ->pluck('id')
            ->toArray();
        $branchManagerRole->permissions()->syncWithoutDetaching($branchManagerPermissions);

        // Accountant - Inventory and Reporting
        $accountantPermissions = $allPermissions
            ->whereIn('name', [
                'view_product',
                'view_category',
                'view_inventory',
                'record_inventory_movement',
                'view_reports',
                'export_reports',
                'view_customer',
                'view_supplier',
                'view_invoice',
                'view_payroll',
                'view_user',
                'view_sales',
                'view_safe',
                'create_safe',
                'edit_safe',
                'delete_safe',
                'deposit_safe',
                'withdraw_safe'
            ])
            ->pluck('id')
            ->toArray();
        $accountantRole->permissions()->syncWithoutDetaching($accountantPermissions);

        // Cashier - Limited inventory and product viewing
        $cashierPermissions = $allPermissions
            ->whereIn('name', [
                'view_product',
                'view_category',
                'view_inventory',
                'record_inventory_movement',
                'view_customer',
                'view_supplier',
                'view_invoice',
                'view_sales',
                'view_safe'
            ])
            ->pluck('id')
            ->toArray();
        $cashierRole->permissions()->syncWithoutDetaching($cashierPermissions);

        // View-Only - Can only view
        $viewOnlyPermissions = $allPermissions
            ->whereIn('name', [
                'view_product',
                'view_category',
                'view_inventory',
                'view_reports',
                'view_customer',
                'view_supplier',
                'view_invoice',
                'view_payroll',
                'view_user',
                'view_sales',
                'view_safe'
            ])
            ->pluck('id')
            ->toArray();
        $viewOnlyRole->permissions()->syncWithoutDetaching($viewOnlyPermissions);
    }
}
