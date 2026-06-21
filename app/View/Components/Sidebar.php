<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Sidebar extends Component
{
    public array $menu;
    public bool $isSuperAdmin;
    public bool $isAdmin;

    public function __construct()
    {
        $user = auth()->user();
        $role = $user?->role?->name ?? null;

        $this->isSuperAdmin = $user?->isSuperAdmin() ?? false;
        $this->isAdmin = $role === 'Admin' || $role === 'admin' || $this->isSuperAdmin;

        if ($this->isSuperAdmin) {
            $this->menu = [
                'main' => [
                    ['route' => 'super-admin.dashboard', 'icon' => 'bi-speedometer2', 'label' => 'messages.live_dashboard'],
                ],
                'reports' => [],
                'systems' => [],
                'admin' => [
                    ['route' => 'super-admin.users.index', 'icon' => 'bi-people', 'label' => 'messages.system_users', 'feature' => 'user_management'],
                    ['route' => 'roles.index', 'icon' => 'bi-shield-lock', 'label' => 'messages.roles_management', 'feature' => 'roles_management'],
                    ['route' => 'super-admin.feature-access.index', 'icon' => 'bi-sliders', 'label' => 'messages.feature_access', 'feature' => 'permissions_management'],
                    ['route' => 'super-admin.merchants.index', 'icon' => 'bi-building', 'label' => 'messages.merchants', 'feature' => 'merchants'],
                    ['route' => 'super-admin.packages.index', 'icon' => 'bi-box-seam', 'label' => 'messages.packages', 'feature' => 'packages'],
                    ['route' => 'super-admin.subscriptions.index', 'icon' => 'bi-bookmark-check', 'label' => 'messages.subscriptions', 'feature' => 'subscriptions'],
                    ['route' => 'super-admin.vat-rates.index', 'icon' => 'bi-percent', 'label' => 'messages.vat_rates', 'feature' => 'vat_rates'],
                    ['route' => 'audit-logs.index', 'icon' => 'bi-journal-text', 'label' => 'messages.audit_logs', 'feature' => 'audit_logs'],
                ],
            ];

            return;
        }

        // Build a clear, predictable menu structure covering requested sections
        $main = [
            ['route' => 'system.dashboard', 'icon' => 'bi-speedometer2', 'label' => 'messages.dashboard', 'feature' => 'dashboard', 'permission' => 'view_reports'],
            ['route' => 'products.index', 'icon' => 'bi-box-seam', 'label' => 'messages.products', 'feature' => 'products', 'permission' => 'view_product'],
            ['route' => 'categories.index', 'icon' => 'bi-tags', 'label' => 'messages.categories', 'feature' => 'categories', 'permission' => 'view_category'],
            ['route' => 'employees.index', 'icon' => 'bi-people', 'label' => 'messages.employees', 'feature' => 'employees', 'permission' => 'view_user'],
            ['route' => 'sales.index', 'icon' => 'bi-cash-coin', 'label' => 'messages.sales', 'feature' => 'sales', 'permission' => 'view_sales'],
        ];

        $suppliers = [
            ['route' => 'suppliers.index', 'icon' => 'bi-truck', 'label' => 'messages.suppliers', 'feature' => 'suppliers', 'permission' => 'view_supplier'],
            ['route' => 'invoices.index', 'icon' => 'bi-receipt', 'label' => 'messages.invoices', 'feature' => 'invoicing', 'permission' => 'view_invoice'],
            ['route' => 'payroll.index', 'icon' => 'bi-wallet2', 'label' => 'messages.payrolls', 'feature' => 'payroll', 'permission' => 'view_payroll'],
            ['route' => 'branches.index', 'icon' => 'bi-diagram-3', 'label' => 'messages.branches', 'feature' => 'branches', 'permission' => 'view_branch'],
        ];

        $reports = [
            ['route' => 'reports.sales', 'icon' => 'bi-graph-up', 'label' => 'messages.sales_report', 'feature' => 'sales_report', 'permission' => 'view_reports'],
            ['route' => 'reports.inventory', 'icon' => 'bi-boxes', 'label' => 'messages.inventory_report', 'feature' => 'inventory_report', 'permission' => 'view_inventory'],
            ['route' => 'reports.financial', 'icon' => 'bi-currency-dollar', 'label' => 'messages.financial_report', 'feature' => 'financial_report', 'permission' => 'view_reports'],
        ];

        $systems = [
            ['route' => 'commissions.index', 'icon' => 'bi-percent', 'label' => 'messages.commissions', 'feature' => 'commissions'],
            ['route' => 'storages.index', 'icon' => 'bi-box-seam', 'label' => 'messages.storages', 'feature' => 'storages'],
            ['route' => 'safes.index', 'icon' => 'bi-safe', 'label' => 'messages.safes', 'feature' => 'safes'],
        ];

        $admin = [];
        if ($this->isAdmin) {
            $admin = [
                ['route' => 'audit-logs.index', 'icon' => 'bi-journal-text', 'label' => 'Audit Logs', 'feature' => 'audit_logs'],
            ];
        }

        // Filter items by feature availability (helper-based), but allow local overrides
        $filter = function ($items) use ($user) {
            return array_values(array_filter($items, function ($it) {
                if (app()->isLocal() && request()->has('debug_menu_all')) {
                    return true;
                }
                $feature = $it['feature'] ?? null;
                $permission = $it['permission'] ?? null;

                if (!$feature && !$permission) {
                    return true;
                }

                return auth()->user()?->canViewMenuItem($feature, $permission) ?? false;
            }));
        };

        $this->menu = [
            'main' => $filter($main),
            'customers' => $filter($suppliers),
            'systems' => $filter($systems),
            'reports' => $filter($reports),
            'admin' => $filter($admin),
        ];

        // Ensure labels are strings to prevent Blade escaping errors
        array_walk_recursive($this->menu, function (&$value, $key) {
            if ($key === 'label' && is_array($value)) {
                if (isset($value['text'])) {
                    $value = (string) $value['text'];
                } else {
                    $value = json_encode($value);
                }
            }
        });
    }

    public function render()
    {
        return view('components.sidebar');
    }
}
