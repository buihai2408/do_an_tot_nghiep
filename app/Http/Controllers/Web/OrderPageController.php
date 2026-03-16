<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OrderPageController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items.toppings'])
            ->latest()
            ->paginate(10)
            ->through(fn($order) => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status->value,
                'status_label' => $order->status->label(),
                'status_color' => $order->status->color(),
                'total' => $order->total,
                'items_count' => $order->items->sum('quantity'),
                'created_at' => $order->created_at->format('d/m/Y H:i'),
            ]);

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        $order->load(['items.toppings', 'coupon']);

        return Inertia::render('Orders/Show', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status->value,
                'status_label' => $order->status->label(),
                'status_color' => $order->status->color(),
                'order_type' => $order->order_type->value,
                'order_type_label' => $order->order_type->label(),
                'subtotal' => $order->subtotal,
                'discount_amount' => $order->discount_amount,
                'shipping_fee' => $order->shipping_fee,
                'total' => $order->total,
                'points_earned' => $order->points_earned ?? 0,
                'points_used' => $order->points_used ?? 0,
                'points_discount' => $order->points_discount ?? 0,
                'payment_method' => $order->payment_method->label(),
                'payment_status' => $order->payment_status->label(),
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'shipping_address' => $order->shipping_address,
                'note' => $order->note,
                'cancel_reason' => $order->cancel_reason,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
                'confirmed_at' => $order->confirmed_at?->format('d/m/Y H:i'),
                'completed_at' => $order->completed_at?->format('d/m/Y H:i'),
                'cancelled_at' => $order->cancelled_at?->format('d/m/Y H:i'),
                'can_cancel' => $order->status === OrderStatus::Pending,
                'can_review' => $order->status === OrderStatus::Completed && !$order->reviews()->where('user_id', Auth::id())->exists(),
                'items' => $order->items->map(fn($item) => [
                    'id' => $item->id,
                    'product_name' => $item->product_name,
                    'size_name' => $item->size_name,
                    'ice_level' => $item->ice_level,
                    'sugar_level' => $item->sugar_level,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                    'toppings' => $item->toppings->map(fn($t) => [
                        'name' => $t->topping_name,
                        'price' => $t->price,
                    ]),
                ]),
            ],
        ]);
    }
}
