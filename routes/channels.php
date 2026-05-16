<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Order;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('admin.orders', function ($user) {
    return $user->isAdmin() || $user->isStaff();
});

Broadcast::channel('orders.{orderId}', function ($user, $orderId) {
    $order = Order::findOrNew($orderId);
    return (int) $user->id === (int) $order->user_id;
});
