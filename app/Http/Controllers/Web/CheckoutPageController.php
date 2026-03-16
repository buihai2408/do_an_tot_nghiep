<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Enums\OrderType;
use App\Enums\PaymentMethod;
use App\Models\User;
use App\Services\CartService;
use App\Services\LoyaltyService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CheckoutPageController extends Controller
{
    public function __invoke(CartService $cartService, LoyaltyService $loyaltyService)
    {
        $cart = $cartService->getCartWithItems();
        $summary = $cartService->getCartSummary();
        /** @var User $user */
        $user = Auth::user();
        $addresses = $user->addresses()->orderByDesc('is_default')->get();

        return Inertia::render('Checkout/Index', [
            'cart' => [
                'items' => $cart->items->map(fn($item) => [
                    'id' => $item->id,
                    'product_name' => $item->product->name,
                    'size' => $item->size?->name,
                    'toppings' => $item->toppings->pluck('name'),
                    'quantity' => $item->quantity,
                    'total' => $item->total,
                ]),
            ],
            'summary' => $summary,
            'addresses' => $addresses,
            'order_types' => collect(OrderType::cases())->map(fn($t) => ['value' => $t->value, 'label' => $t->label()]),
            'payment_methods' => collect(PaymentMethod::cases())->map(fn($m) => ['value' => $m->value, 'label' => $m->label()]),
            'loyalty' => [
                'points' => $user->loyalty_points ?? 0,
                'max_redeemable' => $loyaltyService->getMaxRedeemable($user, $summary['subtotal']),
                'tier' => $loyaltyService->getLoyaltySummary($user),
            ],
        ]);
    }
}
