<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Size;
use App\Models\Topping;

class PricingService
{
    public function calculateItemPrice(int $productId, ?int $sizeId, array $toppingIds = []): array
    {
        $product = Product::findOrFail($productId);
        $sizePrice = $product->base_price;

        if ($sizeId) {
            $pivot = $product->sizes()->where('size_id', $sizeId)->first();
            if ($pivot) {
                $sizePrice = $pivot->pivot->price;
            }
        }

        $toppingsTotal = 0;
        $toppingsDetail = [];
        if (!empty($toppingIds)) {
            $toppings = Topping::whereIn('id', $toppingIds)->active()->get();
            foreach ($toppings as $topping) {
                $toppingsTotal += $topping->price;
                $toppingsDetail[] = [
                    'id' => $topping->id,
                    'name' => $topping->name,
                    'price' => $topping->price,
                ];
            }
        }

        return [
            'base_price' => $sizePrice,
            'toppings_total' => $toppingsTotal,
            'unit_price' => $sizePrice + $toppingsTotal,
            'toppings' => $toppingsDetail,
        ];
    }

    public function calculateCartTotal(array $items): array
    {
        $subtotal = 0;
        foreach ($items as $item) {
            $toppingTotal = collect($item['toppings'] ?? [])->sum(fn($t) => $t['price'] ?? $t['pivot']['price'] ?? 0);
            $subtotal += ($item['unit_price'] + $toppingTotal) * $item['quantity'];
        }

        return [
            'subtotal' => $subtotal,
            'shipping_fee' => $subtotal >= 100000 ? 0 : 25000,
        ];
    }
}
