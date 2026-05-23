<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\Merchant;
use App\Models\Package;
use App\Models\Subscription;
use App\Models\User;
use App\Models\VatRate;
use Carbon\Carbon;
use Illuminate\Support\Str;

class MerchantService
{
    /**
     * Create a new merchant with initial setup
     */
    public function createMerchant(array $data): Merchant
    {
        $superAdmin = auth()->user();
        $merchant = Merchant::create([
            'name' => $data['business_name'],
            'admin_email' => $superAdmin->email,
            'super_admin_id' => $data['super_admin_id'] ?? auth()->id(),
            'business_name' => $data['business_name'],
            'slug' => Str::slug($data['business_name']) . '-' . Str::random(6),
            'default_currency_id' => $data['default_currency_id'],
            'max_currencies' => $data['max_currencies'] ?? 5,
            'max_languages' => $data['max_languages'] ?? 3,
            'default_language' => $data['default_language'] ?? 'en',
            'max_employees' => $data['max_employees'] ?? null,
            'description' => $data['description'] ?? null,
            'is_active' => true,
        ]);

        // Add default currency
        $merchant->currencies()->attach($data['default_currency_id'], ['is_default' => true]);

        // Create initial VAT rate if provided
        if (isset($data['vat_rate'])) {
            VatRate::create([
                'merchant_id' => $merchant->id,
                'rate' => $data['vat_rate'],
                'is_enabled' => true,
            ]);
        }

        return $merchant;
    }

    /**
     * Add a currency to merchant
     */
    public function addCurrency(Merchant $merchant, int $currencyId, bool $isDefault = false): void
    {
        if ($merchant->currencies()->count() >= $merchant->max_currencies) {
            throw new \Exception("Cannot add more currencies. Limit reached: {$merchant->max_currencies}");
        }

        if ($isDefault) {
            $merchant->currencies()->update(['is_default' => false]);
        }

        $merchant->currencies()->attach($currencyId, ['is_default' => $isDefault]);

        if ($isDefault) {
            $merchant->update(['default_currency_id' => $currencyId]);
        }
    }

    /**
     * Remove a currency from merchant
     */
    public function removeCurrency(Merchant $merchant, int $currencyId): void
    {
        if ($merchant->defaultCurrency->id === $currencyId) {
            throw new \Exception("Cannot remove default currency");
        }

        $merchant->currencies()->detach($currencyId);
    }

    /**
     * Create subscription for merchant
     */
    public function createSubscription(Merchant $merchant, Package $package, ?string $paymentMethod = null, ?float $amountPaid = null): Subscription
    {
        // End any existing active subscription
        $merchant->subscription()->where('is_active', true)->update(['is_active' => false]);

        $startsAt = now();
        $expiresAt = now()->addDays($package->duration_days);

        $subscription = Subscription::create([
            'merchant_id' => $merchant->id,
            'package_id' => $package->id,
            'start_date' => $startsAt,
            'expires_at' => $expiresAt,
            'is_active' => true,
            'payment_method' => $paymentMethod,
            'amount_paid' => $amountPaid,
        ]);

        // Update merchant subscription expiry
        $merchant->update(['subscription_expires_at' => $expiresAt]);

        return $subscription;
    }

    /**
     * Renew subscription
     */
    public function renewSubscription(Subscription $subscription): Subscription
    {
        $expiresAt = $subscription->expires_at->addDays($subscription->package->duration_days);

        $subscription->update([
            'expires_at' => $expiresAt,
            'is_active' => true,
        ]);

        $subscription->merchant->update(['subscription_expires_at' => $expiresAt]);

        return $subscription;
    }

    /**
     * Get merchant's active VAT rate
     */
    public function getVatRate(Merchant $merchant): ?VatRate
    {
        return VatRate::getActive($merchant->id);
    }

    /**
     * Set VAT rate for merchant
     */
    public function setVatRate(Merchant $merchant, float $rate, bool $isEnabled = true): VatRate
    {
        $existing = VatRate::where('merchant_id', $merchant->id)->first();

        if ($existing) {
            $existing->update([
                'rate' => $rate,
                'is_enabled' => $isEnabled,
            ]);
            return $existing;
        }

        return VatRate::create([
            'merchant_id' => $merchant->id,
            'rate' => $rate,
            'is_enabled' => $isEnabled,
        ]);
    }

    /**
     * Get default currency for merchant
     */
    public function getDefaultCurrency(Merchant $merchant): Currency
    {
        return $merchant->defaultCurrency;
    }

    /**
     * Get all available currencies for merchant
     */
    public function getAvailableCurrencies(Merchant $merchant): \Illuminate\Support\Collection
    {
        return $merchant->currencies;
    }

    /**
     * Check if merchant can add employees
     */
    public function canAddEmployees(Merchant $merchant): bool
    {
        if ($merchant->max_employees === null) {
            return true; // Unlimited
        }

        $currentCount = $merchant->employees()->count();
        return $currentCount < $merchant->max_employees;
    }

    /**
     * Get remaining employee slots
     */
    public function getRemainingEmployeeSlots(Merchant $merchant): ?int
    {
        if ($merchant->max_employees === null) {
            return null; // Unlimited
        }

        return $merchant->max_employees - $merchant->employees()->count();
    }

    /**
     * Check subscription is valid and active
     */
    public function isSubscriptionValid(Merchant $merchant): bool
    {
        $activeSubscription = $merchant->subscription()
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();

        return $activeSubscription !== null;
    }

    /**
     * Get days until subscription expires
     */
    public function getDaysUntilExpiry(Merchant $merchant): ?int
    {
        if (!$merchant->subscription_expires_at) {
            return null;
        }

        return now()->diffInDays($merchant->subscription_expires_at);
    }
}
