<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Preparing = 'preparing';
    case Ready = 'ready';
    case Delivering = 'delivering';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Chờ xác nhận',
            self::Confirmed => 'Đã xác nhận',
            self::Preparing => 'Đang pha chế',
            self::Ready => 'Sẵn sàng',
            self::Delivering => 'Đang giao',
            self::Completed => 'Hoàn thành',
            self::Cancelled => 'Đã hủy',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::Confirmed => 'blue',
            self::Preparing => 'indigo',
            self::Ready => 'purple',
            self::Delivering => 'orange',
            self::Completed => 'green',
            self::Cancelled => 'red',
        };
    }

    public function canTransitionTo(self $status): bool
    {
        return match ($this) {
            self::Pending => in_array($status, [self::Confirmed, self::Cancelled]),
            self::Confirmed => in_array($status, [self::Preparing, self::Cancelled]),
            self::Preparing => $status === self::Ready,
            self::Ready => in_array($status, [self::Delivering, self::Completed]),
            self::Delivering => $status === self::Completed,
            self::Completed, self::Cancelled => false,
        };
    }
}
