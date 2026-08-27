<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Merchant;
use App\Models\Subscription;
use App\Models\User;
use App\Services\MerchantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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
            'admin_users' => 'nullable|array',
            'admin_users.*.name' => 'required_with:admin_users|string|max:255',
            'admin_users.*.email' => 'required_with:admin_users|email|unique:users,email',
            'admin_users.*.password' => ['required_with:admin_users', 'string', Password::min(8)->mixedCase()->numbers()],
        ]);

        $merchant = $this->merchantService->createMerchant($validated);

        // Create admin users for the merchant
        if (!empty($validated['admin_users'])) {
            foreach ($validated['admin_users'] as $adminData) {
                User::create([
                    'name' => $adminData['name'],
                    'email' => $adminData['email'],
                    'password' => Hash::make($adminData['password']),
                    'user_type' => 'merchant_admin',
                    'merchant_id' => $merchant->id,
                    'is_active' => true,
                ]);
            }
        }

        return redirect()->route('super-admin.merchants.show', $merchant)
            ->with('success', 'Merchant and admin users created successfully');
    }

    /**
     * Show merchant details
     */
    public function show(Merchant $merchant)
    {
        $this->authorize('view', $merchant);

        $merchant->load(['defaultCurrency', 'currencies', 'subscription.package', 'users']);

        $activeSubscription = $merchant->subscription()
            ->where('is_active', true)
            ->where('expires_at', '>', now())
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
     * Show unassigned users and merchants to allow assigning users to merchants
     */
    public function unassigned(Request $request)
    {
        $this->authorize('viewAny', Merchant::class);

        $merchants = Merchant::orderBy('business_name')->get();
        $unassignedUsers = User::whereNull('merchant_id')
            ->where('user_type', '!=', 'super_admin')
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            })
            ->paginate(25);

        return view('super-admin.merchants.unassigned', compact('merchants', 'unassignedUsers'));
    }

    /**
     * Assign a user to a merchant
     */
    public function assignUser(Request $request)
    {
        $this->authorize('update', Merchant::class);

        $validated = $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'user_id' => 'nullable|exists:users,id',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $merchantId = $validated['merchant_id'];

        // Support single user assignment or bulk assignment via user_ids[]
        $ids = [];
        if (!empty($validated['user_id'])) {
            $ids[] = $validated['user_id'];
        }
        if (!empty($validated['user_ids'])) {
            $ids = array_merge($ids, $validated['user_ids']);
        }

        if (empty($ids)) {
            return redirect()->route('super-admin.merchants.unassigned')
                ->withErrors(['user' => 'No users selected to assign']);
        }

        // Ensure we only update users that are unassigned and not super_admin
        $updated = User::whereIn('id', $ids)
            ->whereNull('merchant_id')
            ->where('user_type', '!=', 'super_admin')
            ->update(['merchant_id' => $merchantId]);

        return redirect()->route('super-admin.merchants.unassigned')
            ->with('success', "Assigned {$updated} user(s) to merchant successfully");
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
            'rate_percentage' => 'required|numeric|min:0|max:100',
            'applies_to' => 'required|in:invoices,all',
            'is_active' => 'boolean',
        ]);

        $this->merchantService->setVatRate(
            $merchant,
            (float) $validated['rate_percentage'],
            $validated['applies_to'],
            $request->boolean('is_active', true)
        );

        return redirect()->back()->with('success', 'VAT rate updated successfully');
    }
}
