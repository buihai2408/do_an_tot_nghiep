<?php

namespace App\Enums;

enum OrderType: string
{
    case Delivery = 'delivery';
    case Pickup = 'pickup';

    public function label(): string
    {
        return match ($this) {
            self::Delivery => 'Giao hàng',
            self::Pickup => 'Nhận tại quán',
        };
    }
}
