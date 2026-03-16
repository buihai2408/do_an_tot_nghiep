<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Topping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartService
{
    protected PricingService $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    public function getOrCreateCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['session_id' => null]
            );
        }

        $sessionId = session()->getId();
        return Cart::firstOrCreate(
            ['session_id' => $sessionId],
            ['user_id' => null]
        );
    }

    public function getCartWithItems(): Cart
    {
        $cart = $this->getOrCreateCart();
        $cart->load(['items.product', 'items.size', 'items.toppings']);
        return $cart;
    }

    public function addItem(array $data): CartItem
    {
        $cart = $this->getOrCreateCart();

        $pricing = $this->pricingService->calculateItemPrice(
            $data['product_id'],
            $data['size_id'] ?? null,
            $data['topping_ids'] ?? []
        );

        return DB::transaction(function () use ($cart, $data, $pricing) {
            $item = $cart->items()->create([
                'product_id' => $data['product_id'],
                'size_id' => $data['size_id'] ?? null,
                'ice_level' => $data['ice_level'] ?? 'normal',
                'sugar_level' => $data['sugar_level'] ?? 'normal',
                'quantity' => $data['quantity'] ?? 1,
                'unit_price' => $pricing['base_price'],
            ]);

            if (!empty($data['topping_ids'])) {
                $toppings = Topping::whereIn('id', $data['topping_ids'])->active()->get();
                foreach ($toppings as $topping) {
                    $item->toppings()->attach($topping->id, ['price' => $topping->price]);
                }
            }

            return $item->load(['product', 'size', 'toppings']);
        });
    }

    public function updateItem(CartItem $item, array $data): CartItem
    {
        $item->update([
            'quantity' => $data['quantity'],
        ]);

        return $item->fresh(['product', 'size', 'toppings']);
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    public function clearCart(): void
    {
        $cart = $this->getOrCreateCart();
        $cart->items()->delete();
    }

    public function mergeGuestCart(): void
    {
        if (!Auth::check()) return;

        $sessionId = session()->getId();
        $guestCart = Cart::where('session_id', $sessionId)->first();

        if (!$guestCart || $guestCart->items->isEmpty()) return;

        $userCart = Cart::firstOrCreate(
            ['user_id' => Auth::id()],
            ['session_id' => null]
        );

        DB::transaction(function () use ($guestCart, $userCart) {
            foreach ($guestCart->items as $item) {
                $newItem = $userCart->items()->create([
                    'product_id' => $item->product_id,
                    'size_id' => $item->size_id,
                    'ice_level' => $item->ice_level,
                    'sugar_level' => $item->sugar_level,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                ]);

                foreach ($item->toppings as $topping) {
                    $newItem->toppings()->attach($topping->id, ['price' => $topping->pivot->price]);
                }
            }

            $guestCart->items()->delete();
            $guestCart->delete();
        });
    }

    public function getCartSummary(): array
    {
        $cart = $this->getCartWithItems();
        $items = $cart->items;

        $subtotal = $items->sum(function ($item) {
            $toppingTotal = $item->toppings->sum('pivot.price');
            return ($item->unit_price + $toppingTotal) * $item->quantity;
        });

        return [
            'items_count' => $items->sum('quantity'),
            'subtotal' => $subtotal,
            'shipping_fee' => $subtotal >= 100000 ? 0 : 25000,
            'total' => $subtotal + ($subtotal >= 100000 ? 0 : 25000),
        ];
    }
}
