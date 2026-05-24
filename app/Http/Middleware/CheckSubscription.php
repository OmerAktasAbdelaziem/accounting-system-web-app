<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\MerchantService;

class CheckSubscription
{
    protected $merchantService;

    public function __construct(MerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && !$user->isSuperAdmin() && $user->merchant) {
            $merchant = $user->merchant;
            $valid = $this->merchantService->isSubscriptionValid($merchant);
            if (!$valid) {
                // Share a flag and details with views so frontend can block UI
                view()->share('subscription_blocked', true);
                view()->share('subscription_block_details', [
                    'merchant' => $merchant->business_name,
                    'expires_at' => optional($merchant->subscription()->orderByDesc('expires_at')->first())->expires_at?->toDateTimeString(),
                ]);
            } else {
                view()->share('subscription_blocked', false);
            }
        }

        return $next($request);
    }
}
