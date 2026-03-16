<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemTopping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected CartService $cartService;
    protected CouponService $couponService;
    protected LoyaltyService $loyaltyService;

    public function __construct(CartService $cartService, CouponService $couponService, LoyaltyService $loyaltyService)
    {
        $this->cartService = $cartService;
        $this->couponService = $couponService;
        $this->loyaltyService = $loyaltyService;
    }

    public function placeOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $cart = $this->cartService->getCartWithItems();

            if ($cart->items->isEmpty()) {
                throw new \Exception('Giỏ hàng trống.');
            }

            $subtotal = $cart->items->sum(function ($item) {
                $toppingTotal = $item->toppings->sum('pivot.price');
                return ($item->unit_price + $toppingTotal) * $item->quantity;
            });

            $discountAmount = 0;
            $coupon = null;

            if (!empty($data['coupon_code'])) {
                $result = $this->couponService->validate($data['coupon_code'], $subtotal);
                if ($result['valid']) {
                    $coupon = $result['coupon'];
                    $discountAmount = $result['discount'];
                }
            }

            $shippingFee = ($data['order_type'] ?? 'delivery') === 'delivery'
                ? ($subtotal >= 100000 ? 0 : 25000)
                : 0;

            $user = Auth::user();

            $pointsUsed = 0;
            $pointsDiscount = 0;
            if (!empty($data['points_used']) && $data['points_used'] > 0 && $user->loyalty_points > 0) {
                $maxRedeemable = $this->loyaltyService->getMaxRedeemable($user, $subtotal);
                $pointsUsed = min((int) $data['points_used'], $maxRedeemable);
                $pointsDiscount = $pointsUsed * 1000;
            }

            $total = $subtotal - $discountAmount - $pointsDiscount + $shippingFee;

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => Order::generateOrderNumber(),
                'status' => OrderStatus::Pending,
                'order_type' => $data['order_type'] ?? 'delivery',
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'shipping_fee' => $shippingFee,
                'total' => $total,
                'points_used' => $pointsUsed,
                'points_discount' => $pointsDiscount,
                'coupon_id' => $coupon?->id,
                'payment_method' => $data['payment_method'] ?? 'cod',
                'payment_status' => PaymentStatus::Pending,
                'customer_name' => $data['customer_name'] ?? $user->name,
                'customer_phone' => $data['customer_phone'] ?? $user->phone,
                'customer_email' => $data['customer_email'] ?? $user->email,
                'shipping_address' => $data['shipping_address'] ?? null,
                'note' => $data['note'] ?? null,
            ]);

            foreach ($cart->items as $cartItem) {
                $toppingTotal = $cartItem->toppings->sum('pivot.price');
                $itemSubtotal = ($cartItem->unit_price + $toppingTotal) * $cartItem->quantity;

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'size_name' => $cartItem->size?->name,
                    'ice_level' => $cartItem->ice_level?->value,
                    'sugar_level' => $cartItem->sugar_level?->value,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price,
                    'subtotal' => $itemSubtotal,
                ]);

                foreach ($cartItem->toppings as $topping) {
                    OrderItemTopping::create([
                        'order_item_id' => $orderItem->id,
                        'topping_name' => $topping->name,
                        'price' => $topping->pivot->price,
                    ]);
                }
            }

            if ($coupon) {
                $this->couponService->apply($coupon, $user->id, $order->id, $discountAmount);
            }

            if ($pointsUsed > 0) {
                $this->loyaltyService->redeemPoints($user, $pointsUsed, $order);
            }

            $this->cartService->clearCart();

            return $order->load(['items.toppings']);
        });
    }

    public function transition(Order $order, OrderStatus $newStatus, ?string $reason = null): Order
    {
        if (!$order->status->canTransitionTo($newStatus)) {
            throw new \Exception("Không thể chuyển trạng thái từ {$order->status->label()} sang {$newStatus->label()}.");
        }

        $updateData = ['status' => $newStatus];

        if ($newStatus === OrderStatus::Confirmed) {
            $updateData['confirmed_at'] = now();
        } elseif ($newStatus === OrderStatus::Completed) {
            $updateData['completed_at'] = now();
            $updateData['payment_status'] = PaymentStatus::Paid;
            $order->update($updateData);
            $this->loyaltyService->earnPoints($order->user, $order->fresh());
            return $order->fresh();
        } elseif ($newStatus === OrderStatus::Cancelled) {
            $updateData['cancelled_at'] = now();
            $updateData['cancel_reason'] = $reason;
        }

        $order->update($updateData);

        return $order->fresh();
    }

    public function cancel(Order $order, string $reason): Order
    {
        return $this->transition($order, OrderStatus::Cancelled, $reason);
    }
}
