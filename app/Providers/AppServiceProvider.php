<?php

namespace App\Providers;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Commission;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Safe;
use App\Models\SafeTransaction;
use App\Models\Storage;
use App\Models\StorageItem;
use App\Models\StorageTransfer;
use App\Models\Setting;
use App\Models\Warehouse;
use App\Models\User;
use App\Policies\AuditLogPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\CommissionPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\SafePolicy;
use App\Policies\SafeTransactionPolicy;
use App\Policies\StorageItemPolicy;
use App\Policies\StoragePolicy;
use App\Policies\StorageTransferPolicy;
use App\Policies\WarehousePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!function_exists('currentCurrency')) {
            function currentCurrency(): ?Currency
            {
                static $currency = null;

                if ($currency === null) {
                    $currencyCode = (string) Setting::get('currency', 'AED');
                    $currency = Currency::byCode($currencyCode);
                }

                return $currency;
            }
        }

        if (!function_exists('currencySymbol')) {
            function currencySymbol(string $default = '$'): string
            {
                return currentCurrency()?->symbol ?? $default;
            }
        }

        if (!function_exists('formatCurrency')) {
            function formatCurrency(float|int $amount, ?int $decimalPlaces = null): string
            {
                $currency = currentCurrency();
                $symbol = $currency?->symbol ?? '$';
                $places = $decimalPlaces ?? $currency?->decimal_places ?? 2;

                return $symbol . number_format((float) $amount, $places, '.', ',');
            }
        }

        Gate::policy(Commission::class, CommissionPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Safe::class, SafePolicy::class);
        Gate::policy(SafeTransaction::class, SafeTransactionPolicy::class);
        Gate::policy(Storage::class, StoragePolicy::class);
        Gate::policy(StorageItem::class, StorageItemPolicy::class);
        Gate::policy(StorageTransfer::class, StorageTransferPolicy::class);
        Gate::policy(Warehouse::class, WarehousePolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(AuditLog::class, AuditLogPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        
        // Only force HTTPS in production environment
        if (app()->environment('production')) {
            \URL::forceScheme('https');
        }
    }
}
