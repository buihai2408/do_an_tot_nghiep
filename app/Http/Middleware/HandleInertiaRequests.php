<?php

namespace App\Http\Middleware;

use App\Services\CartService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $cartService = app(CartService::class);

        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
                'loyalty_tier' => fn () => $user?->loyalty_tier?->value,
                'loyalty_tier_label' => fn () => $user?->loyalty_tier?->label(),
                'loyalty_tier_icon' => fn () => $user?->loyalty_tier?->icon(),
                'loyalty_points' => fn () => $user?->loyalty_points ?? 0,
            ],
            'cart_count' => fn () => $cartService->getCartSummary()['items_count'] ?? 0,
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
