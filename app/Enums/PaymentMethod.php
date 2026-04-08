<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cod = 'cod';
    case PayOS = 'payos';

    public function label(): string
    {
        return match ($this) {
            self::Cod => 'Thanh toán khi nhận hàng',
            self::PayOS => 'Thanh toán QR (PayOS)',
        };
    }
}
