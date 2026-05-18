<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Package;
use App\Models\Subscription;
use App\Services\MerchantService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(protected MerchantService $merchantService)
    {
    }

    /**
     * Display all subscriptions
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Subscription::class);

        $subscriptions = Subscription::query()
            ->with(['merchant', 'package'])
            ->when($request->status, function ($q) use ($request) {
                if ($request->status === 'active') {
                    $q->where('is_active', true)->where('expires_at', '>', now());
                } elseif ($request->status === 'inactive') {
                    $q->where('is_active', false);
                } elseif ($request->status === 'expired') {
                    $q->where('is_active', true)->where('expires_at', '<=', now());
                }
            })
            ->when($request->merchant_id, function ($q) use ($request) {
                $q->where('merchant_id', $request->merchant_id);
            })
            ->latest()
            ->paginate(15);

        $merchants = Merchant::pluck('name', 'id');

        return view('super-admin.subscriptions.index', compact('subscriptions', 'merchants'));
    }

    /**
     * Create new subscription
     */
    public function create(Request $request)
    {
        $this->authorize('create', Subscription::class);

        $merchants = Merchant::where('is_active', true)->get();
        $packages = Package::where('is_active', true)->get();

        $merchantId = $request->merchant_id;
        $selectedMerchant = null;

        if ($merchantId) {
            $selectedMerchant = Merchant::findOrFail($merchantId);
        }

        return view('super-admin.subscriptions.create', compact('merchants', 'packages', 'selectedMerchant'));
    }

    /**
     * Store new subscription
     */
    public function store(Request $request)
    {
        $this->authorize('create', Subscription::class);

        $validated = $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'package_id' => 'required|exists:packages,id',
            'payment_method' => 'nullable|string|in:manual,card,bank,cheque',
            'amount_paid' => 'nullable|numeric|min:0',
        ]);

        $merchant = Merchant::findOrFail($validated['merchant_id']);
        $package = Package::findOrFail($validated['package_id']);

        $subscription = $this->merchantService->createSubscription(
            $merchant,
            $package,
            $validated['payment_method'] ?? null,
            $validated['amount_paid'] ?? null
        );

        return redirect()->route('super-admin.subscriptions.show', $subscription)
            ->with('success', 'Subscription created successfully');
    }

    /**
     * Show subscription details
     */
    public function show(Subscription $subscription)
    {
        $this->authorize('view', $subscription);

        $subscription->load(['merchant', 'package.features']);

        return view('super-admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Show renewal form
     */
    public function renewForm(Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        return view('super-admin.subscriptions.renew', compact('subscription'));
    }

    /**
     * Renew subscription with custom duration
     */
    public function renewStore(Request $request, Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $validated = $request->validate([
            'renewal_option' => 'nullable|in:1,3,6,12',
            'custom_days' => 'nullable|numeric|min:1',
        ]);

        // Calculate extension days
        if ($request->custom_days) {
            $days = (int) $request->custom_days;
        } else {
            $months = (int) ($validated['renewal_option'] ?? 1);
            $days = $subscription->package->duration_days * $months;
        }

        $oldExpiry = $subscription->expires_at;
        $subscription->expires_at = $oldExpiry->addDays($days);
        $subscription->save();

        if ($request->send_confirmation) {
            // Send confirmation email
        }

        return redirect()->route('super-admin.subscriptions.show', $subscription)
            ->with('success', 'Subscription renewed successfully until ' . $subscription->expires_at->format('Y-m-d'));
    }

    /**
     * Renew subscription
     */
    public function renew(Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $renewed = $this->merchantService->renewSubscription($subscription);

        return redirect()->route('super-admin.subscriptions.show', $renewed)
            ->with('success', 'Subscription renewed successfully until ' . $renewed->expires_at->format('Y-m-d'));
    }

    /**
     * Cancel/Destroy subscription
     */
    public function destroy(Subscription $subscription)
    {
        $this->authorize('delete', $subscription);

        $merchant = $subscription->merchant;
        $subscription->update(['is_active' => false]);
        $subscription->delete();

        return redirect()->route('super-admin.subscriptions.index')
            ->with('success', "Subscription for {$merchant->name} has been cancelled.");
    }

    /**
     * Update subscription (extend)
     */
    public function update(Request $request, Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $validated = $request->validate([
            'days' => 'required|numeric|min:1',
        ]);

        $oldExpiry = $subscription->expires_at;
        $subscription->expires_at = $oldExpiry->addDays($validated['days']);
        $subscription->save();

        return redirect()->route('super-admin.subscriptions.show', $subscription)
            ->with('success', 'Subscription extended by ' . $validated['days'] . ' days until ' . $subscription->expires_at->format('Y-m-d'));
    }

    /**
     * Show cancellation confirmation page
     */
    public function cancel(Subscription $subscription)
    {
        $this->authorize('delete', $subscription);

        return view('super-admin.subscriptions.cancel', compact('subscription'));
    }

    /**
     * Expire subscription
     */
    public function expire(Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $subscription->expire();

        return redirect()->route('super-admin.subscriptions.show', $subscription)
            ->with('success', 'Subscription marked as expired');
    }
}
