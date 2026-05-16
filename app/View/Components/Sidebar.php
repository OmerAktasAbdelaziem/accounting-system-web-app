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
                    ['route' => 'super-admin.dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
                ],
                'customers' => [],
                'reports' => [],
                'systems' => [],
                'admin' => [
                    ['route' => 'super-admin.users.index', 'icon' => 'bi-people', 'label' => 'System Users'],
                    ['route' => 'super-admin.merchants.index', 'icon' => 'bi-building', 'label' => 'Merchants'],
                    ['route' => 'super-admin.packages.index', 'icon' => 'bi-box-seam', 'label' => 'Packages'],
                    ['route' => 'super-admin.subscriptions.index', 'icon' => 'bi-bookmark-check', 'label' => 'Subscriptions'],
                    ['route' => 'super-admin.feature-access.index', 'icon' => 'bi-toggles2', 'label' => 'Feature Access'],
                    ['route' => 'super-admin.vat-rates.index', 'icon' => 'bi-percent', 'label' => 'VAT Rates'],
                    ['route' => 'profile', 'icon' => 'bi-person', 'label' => 'Profile'],
                    ['route' => 'settings.index', 'icon' => 'bi-gear', 'label' => 'Settings'],
                    ['route' => 'audit-logs.index', 'icon' => 'bi-journal-text', 'label' => 'Audit Logs'],
                ],
            ];

            return;
        }

        // Build a clear, predictable menu structure covering requested sections
        $main = [
            ['route' => 'system.dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
            ['route' => 'products.index', 'icon' => 'bi-box-seam', 'label' => 'Products', 'feature' => 'products'],
            ['route' => 'categories.index', 'icon' => 'bi-tags', 'label' => 'Categories', 'feature' => 'categories'],
            ['route' => 'employees.index', 'icon' => 'bi-people', 'label' => 'Employees', 'feature' => 'employees'],
        ];

        $customers = [
            ['route' => 'customers.index', 'icon' => 'bi-people-fill', 'label' => 'Customers', 'feature' => 'customers'],
            ['route' => 'suppliers.index', 'icon' => 'bi-truck', 'label' => 'Suppliers', 'feature' => 'suppliers'],
            ['route' => 'invoices.index', 'icon' => 'bi-receipt', 'label' => 'Invoices', 'feature' => 'invoicing'],
            ['route' => 'payroll.index', 'icon' => 'bi-wallet2', 'label' => 'Payroll', 'feature' => 'payroll'],
            ['route' => 'branches.index', 'icon' => 'bi-diagram-3', 'label' => 'Branches', 'feature' => 'branches'],
        ];

        $reports = [
            ['route' => 'reports.sales', 'icon' => 'bi-graph-up', 'label' => 'Sales Report', 'feature' => 'sales_report'],
            ['route' => 'reports.inventory', 'icon' => 'bi-boxes', 'label' => 'Inventory Report', 'feature' => 'inventory_report'],
            ['route' => 'reports.financial', 'icon' => 'bi-currency-dollar', 'label' => 'Financial Report', 'feature' => 'financial_report'],
        ];

        $systems = [
            ['route' => 'commissions.index', 'icon' => 'bi-percent', 'label' => 'Commissions', 'feature' => 'commissions'],
            ['route' => 'storages.index', 'icon' => 'bi-box-seam', 'label' => 'Storages', 'feature' => 'storages'],
            ['route' => 'safes.index', 'icon' => 'bi-safe', 'label' => 'Safes', 'feature' => 'safes'],
        ];

        $admin = [];
        if ($this->isAdmin) {
            $admin = [
                ['route' => 'profile', 'icon' => 'bi-person', 'label' => 'Profile'],
                ['route' => 'settings.index', 'icon' => 'bi-gear', 'label' => 'Settings'],
                ['route' => 'roles.index', 'icon' => 'bi-shield-alt', 'label' => 'Roles Management'],
                ['route' => 'audit-logs.index', 'icon' => 'bi-journal-text', 'label' => 'Audit Logs'],
            ];
        }

        // Filter items by feature availability (helper-based), but allow local overrides
        $filter = function ($items) {
            return array_values(array_filter($items, function ($it) {
                if (app()->isLocal() && request()->has('debug_menu_all')) {
                    return true;
                }
                if (! isset($it['feature'])) return true;
                if (function_exists('hasFeature')) {
                    return hasFeature($it['feature']);
                }
                return true;
            }));
        };

        $this->menu = [
            'main' => $filter($main),
            'customers' => $filter($customers),
            'reports' => $filter($reports),
            'systems' => $filter($systems),
            'admin' => $admin,
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
