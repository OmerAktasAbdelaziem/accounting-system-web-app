<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $permissions = [
            ['name' => 'view_sales', 'name_ar' => 'عرض المبيعات', 'description' => 'Can view the sales page and records', 'category' => 'Sales Management'],
            ['name' => 'view_safe', 'name_ar' => 'عرض الخزنة', 'description' => 'Can view safes', 'category' => 'Safe Management'],
            ['name' => 'create_safe', 'name_ar' => 'إنشاء خزنة', 'description' => 'Can create safes', 'category' => 'Safe Management'],
            ['name' => 'edit_safe', 'name_ar' => 'تعديل خزنة', 'description' => 'Can edit safes', 'category' => 'Safe Management'],
            ['name' => 'delete_safe', 'name_ar' => 'حذف خزنة', 'description' => 'Can delete safes', 'category' => 'Safe Management'],
            ['name' => 'deposit_safe', 'name_ar' => 'إيداع الخزنة', 'description' => 'Can deposit into safes', 'category' => 'Safe Management'],
            ['name' => 'withdraw_safe', 'name_ar' => 'سحب من الخزنة', 'description' => 'Can withdraw from safes', 'category' => 'Safe Management'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                [
                    'name_ar' => $permission['name_ar'],
                    'description' => $permission['description'],
                    'category' => $permission['category'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('permissions')->whereIn('name', [
            'view_sales',
            'view_safe',
            'create_safe',
            'edit_safe',
            'delete_safe',
            'deposit_safe',
            'withdraw_safe',
        ])->delete();
    }
};