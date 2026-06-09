<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                    ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                    ->orWhere('customer_phone', 'like', '%' . $request->search . '%');
            });
        }

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $query->latest()->paginate(15)->through(fn($order) => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'status' => $order->status->value,
                'status_label' => $order->status->label(),
                'status_color' => $order->status->color(),
                'total' => $order->total,
                'payment_method' => $order->payment_method->label(),
                'payment_status' => $order->payment_status->label(),
                'created_at' => $order->created_at->format('d/m/Y H:i'),
            ]),
            'statuses' => collect(OrderStatus::cases())->map(fn($s) => ['value' => $s->value, 'label' => $s->label()]),
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['items.toppings', 'user', 'coupon']);
        $allowedTransitions = collect(OrderStatus::cases())
            ->filter(fn($s) => $order->status->canTransitionTo($s, $order->order_type))
            ->map(fn($s) => ['value' => $s->value, 'label' => $s->label()])
            ->values();

        return Inertia::render('Admin/Orders/Show', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status->value,
                'status_label' => $order->status->label(),
                'order_type' => $order->order_type->label(),
                'subtotal' => $order->subtotal,
                'discount_amount' => $order->discount_amount,
                'shipping_fee' => $order->shipping_fee,
                'total' => $order->total,
                'payment_method' => $order->payment_method->label(),
                'payment_status' => $order->payment_status->label(),
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'customer_email' => $order->customer_email,
                'shipping_address' => $order->shipping_address,
                'note' => $order->note,
                'cancel_reason' => $order->cancel_reason,
                'coupon' => $order->coupon ? ['code' => $order->coupon->code, 'name' => $order->coupon->name] : null,
                'created_at' => $order->created_at->format('d/m/Y H:i'),
                'confirmed_at' => $order->confirmed_at?->format('d/m/Y H:i'),
                'completed_at' => $order->completed_at?->format('d/m/Y H:i'),
                'items' => $order->items->map(fn($item) => [
                    'product_name' => $item->product_name,
                    'size_name' => $item->size_name,
                    'ice_level' => $item->ice_level,
                    'sugar_level' => $item->sugar_level,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                    'toppings' => $item->toppings->map(fn($t) => ['name' => $t->topping_name, 'price' => $t->price]),
                ]),
            ],
            'allowedTransitions' => $allowedTransitions,
        ]);
    }
}
