<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Merchant;
use App\Models\Subscription;
use App\Services\MerchantService;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    public function __construct(protected MerchantService $merchantService)
    {
    }

    /**
     * Display all merchants (super admin only)
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Merchant::class);

        $merchants = Merchant::query()
            ->with(['defaultCurrency', 'superAdmin', 'subscription'])
            ->when($request->search, function ($q) use ($request) {
                $q->where('business_name', 'like', "%{$request->search}%")
                  ->orWhere('slug', 'like', "%{$request->search}%");
            })
            ->when($request->status, function ($q) use ($request) {
                if ($request->status === 'active') {
                    $q->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    $q->where('is_active', false);
                } elseif ($request->status === 'expired') {
                    $q->whereNotNull('subscription_expires_at')
                      ->where('subscription_expires_at', '<', now());
                }
            })
            ->paginate(15);

        return view('super-admin.merchants.index', compact('merchants'));
    }

    /**
     * Show merchant creation form
     */
    public function create()
    {
        $this->authorize('create', Merchant::class);

        $currencies = Currency::all();
        return view('super-admin.merchants.create', compact('currencies'));
    }

    /**
     * Store new merchant
     */
    public function store(Request $request)
    {
        $this->authorize('create', Merchant::class);

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'default_currency_id' => 'required|exists:currencies,id',
            'max_currencies' => 'required|integer|min:1|max:20',
            'max_languages' => 'required|integer|min:1|max:10',
            'default_language' => 'required|string|in:en,ar,tr',
            'max_employees' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $merchant = $this->merchantService->createMerchant($validated);

        return redirect()->route('super-admin.merchants.show', $merchant)
            ->with('success', 'Merchant created successfully');
    }

    /**
     * Show merchant details
     */
    public function show(Merchant $merchant)
    {
        $this->authorize('view', $merchant);

        $merchant->load(['defaultCurrency', 'currencies', 'subscription.package', 'users']);

        $activeSubscription = $merchant->subscription()
            ->where('status', 'active')
            ->latest()
            ->first();

        return view('super-admin.merchants.show', [
            'merchant' => $merchant,
            'activeSubscription' => $activeSubscription,
            'daysRemaining' => $this->merchantService->getDaysUntilExpiry($merchant),
            'employeeSlots' => $this->merchantService->getRemainingEmployeeSlots($merchant),
        ]);
    }

    /**
     * Show merchant edit form
     */
    public function edit(Merchant $merchant)
    {
        $this->authorize('update', $merchant);

        $currencies = Currency::all();
        return view('super-admin.merchants.edit', compact('merchant', 'currencies'));
    }

    /**
     * Update merchant
     */
    public function update(Request $request, Merchant $merchant)
    {
        $this->authorize('update', $merchant);

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'max_currencies' => 'required|integer|min:1|max:20',
            'max_languages' => 'required|integer|min:1|max:10',
            'default_language' => 'required|string|in:en,ar,tr',
            'max_employees' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $merchant->update($validated);

        return redirect()->route('super-admin.merchants.show', $merchant)
            ->with('success', 'Merchant updated successfully');
    }

    /**
     * Delete merchant
     */
    public function destroy(Merchant $merchant)
    {
        $this->authorize('delete', $merchant);

        $merchant->delete();

        return redirect()->route('super-admin.merchants.index')
            ->with('success', 'Merchant deleted successfully');
    }

    /**
     * Add currency to merchant
     */
    public function addCurrency(Request $request, Merchant $merchant)
    {
        $this->authorize('update', $merchant);

        $validated = $request->validate([
            'currency_id' => 'required|exists:currencies,id',
            'is_default' => 'boolean',
        ]);

        try {
            $this->merchantService->addCurrency(
                $merchant,
                $validated['currency_id'],
                $validated['is_default'] ?? false
            );

            return redirect()->back()->with('success', 'Currency added successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove currency from merchant
     */
    public function removeCurrency(Merchant $merchant, Currency $currency)
    {
        $this->authorize('update', $merchant);

        try {
            $this->merchantService->removeCurrency($merchant, $currency->id);
            return redirect()->back()->with('success', 'Currency removed successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update VAT rate
     */
    public function updateVat(Request $request, Merchant $merchant)
    {
        $this->authorize('update', $merchant);

        $validated = $request->validate([
            'rate' => 'required|numeric|min:0|max:100',
            'is_enabled' => 'boolean',
        ]);

        $this->merchantService->setVatRate($merchant, $validated['rate'], $validated['is_enabled'] ?? true);

        return redirect()->back()->with('success', 'VAT rate updated successfully');
    }
}
