<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Inertia\Inertia;

class CartPageController extends Controller
{
    public function __invoke(CartService $cartService)
    {
        $cart = $cartService->getCartWithItems();
        $summary = $cartService->getCartSummary();

        return Inertia::render('Cart/Index', [
            'cart' => [
                'items' => $cart->items->map(fn($item) => [
                    'id' => $item->id,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'slug' => $item->product->slug,
                        'image' => $item->product->primaryImage?->path,
                    ],
                    'size' => $item->size ? ['name' => $item->size->name, 'label' => $item->size->label] : null,
                    'ice_level' => $item->ice_level?->value,
                    'sugar_level' => $item->sugar_level?->value,
                    'toppings' => $item->toppings->map(fn($t) => [
                        'id' => $t->id,
                        'name' => $t->name,
                        'price' => $t->pivot->price,
                    ]),
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total,
                ]),
            ],
            'summary' => $summary,
        ]);
    }
}
