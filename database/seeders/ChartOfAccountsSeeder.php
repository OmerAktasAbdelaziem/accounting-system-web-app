<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assets (الأصول)
        $assets = ChartOfAccount::create([
            'account_code' => '1000',
            'account_name' => 'Assets',
            'account_name_ar' => 'الأصول',
            'account_type' => 'asset',
            'description' => 'Current and Fixed Assets',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'account_code' => '1010',
            'account_name' => 'Cash',
            'account_name_ar' => 'النقد',
            'account_type' => 'asset',
            'parent_account_id' => $assets->id,
            'description' => 'Cash in hand and bank deposits',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'account_code' => '1020',
            'account_name' => 'Accounts Receivable',
            'account_name_ar' => 'الذمم المدينة',
            'account_type' => 'asset',
            'parent_account_id' => $assets->id,
            'description' => 'Money owed by customers',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'account_code' => '1030',
            'account_name' => 'Inventory',
            'account_name_ar' => 'المخزون',
            'account_type' => 'asset',
            'parent_account_id' => $assets->id,
            'description' => 'Merchandise and products',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        // Liabilities (الالتزامات)
        $liabilities = ChartOfAccount::create([
            'account_code' => '2000',
            'account_name' => 'Liabilities',
            'account_name_ar' => 'الالتزامات',
            'account_type' => 'liability',
            'description' => 'Current and Long-term Liabilities',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'account_code' => '2010',
            'account_name' => 'Accounts Payable',
            'account_name_ar' => 'الذمم الدائنة',
            'account_type' => 'liability',
            'parent_account_id' => $liabilities->id,
            'description' => 'Money owed to suppliers',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'account_code' => '2020',
            'account_name' => 'Salaries Payable',
            'account_name_ar' => 'الرواتب المستحقة',
            'account_type' => 'liability',
            'parent_account_id' => $liabilities->id,
            'description' => 'Employee salaries due',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        // Equity (رأس المال)
        $equity = ChartOfAccount::create([
            'account_code' => '3000',
            'account_name' => 'Equity',
            'account_name_ar' => 'رأس المال',
            'account_type' => 'equity',
            'description' => 'Owner\'s equity and retained earnings',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'account_code' => '3010',
            'account_name' => 'Capital',
            'account_name_ar' => 'رأس المال المدفوع',
            'account_type' => 'equity',
            'parent_account_id' => $equity->id,
            'description' => 'Paid-in capital',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'account_code' => '3020',
            'account_name' => 'Retained Earnings',
            'account_name_ar' => 'الأرباح المحتفظ بها',
            'account_type' => 'equity',
            'parent_account_id' => $equity->id,
            'description' => 'Accumulated profits',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        // Revenue (الإيرادات)
        $revenue = ChartOfAccount::create([
            'account_code' => '4000',
            'account_name' => 'Revenue',
            'account_name_ar' => 'الإيرادات',
            'account_type' => 'revenue',
            'description' => 'Operating revenue and income',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'account_code' => '4010',
            'account_name' => 'Sales Revenue',
            'account_name_ar' => 'إيرادات المبيعات',
            'account_type' => 'revenue',
            'parent_account_id' => $revenue->id,
            'description' => 'Revenue from sales of products',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'account_code' => '4020',
            'account_name' => 'Service Revenue',
            'account_name_ar' => 'إيرادات الخدمات',
            'account_type' => 'revenue',
            'parent_account_id' => $revenue->id,
            'description' => 'Revenue from services',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        // Expenses (المصاريف)
        $expenses = ChartOfAccount::create([
            'account_code' => '5000',
            'account_name' => 'Expenses',
            'account_name_ar' => 'المصاريف',
            'account_type' => 'expense',
            'description' => 'Operating expenses and costs',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'account_code' => '5010',
            'account_name' => 'Cost of Goods Sold',
            'account_name_ar' => 'تكلفة البضاعة المباعة',
            'account_type' => 'expense',
            'parent_account_id' => $expenses->id,
            'description' => 'Cost of inventory sold',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'account_code' => '5020',
            'account_name' => 'Salaries Expense',
            'account_name_ar' => 'مصاريف الرواتب',
            'account_type' => 'expense',
            'parent_account_id' => $expenses->id,
            'description' => 'Employee salaries and wages',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'account_code' => '5030',
            'account_name' => 'Rent Expense',
            'account_name_ar' => 'مصاريف الإيجار',
            'account_type' => 'expense',
            'parent_account_id' => $expenses->id,
            'description' => 'Rent for facilities',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'account_code' => '5040',
            'account_name' => 'Utilities Expense',
            'account_name_ar' => 'مصاريف الخدمات',
            'account_type' => 'expense',
            'parent_account_id' => $expenses->id,
            'description' => 'Electricity, water, phone, etc.',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'account_code' => '5050',
            'account_name' => 'Office Supplies',
            'account_name_ar' => 'مستلزمات المكتب',
            'account_type' => 'expense',
            'parent_account_id' => $expenses->id,
            'description' => 'Office supplies and materials',
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'account_code' => '5060',
            'account_name' => 'Marketing Expense',
            'account_name_ar' => 'مصاريف التسويق',
            'account_type' => 'expense',
            'parent_account_id' => $expenses->id,
            'description' => 'Advertising and promotion costs',
            'opening_balance' => 0,
            'is_active' => true,
        ]);
    }
}
