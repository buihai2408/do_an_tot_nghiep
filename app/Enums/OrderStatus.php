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

    /**
     * Kiểm tra luồng chuyển trạng thái hợp lệ.
     *
     * Delivery (Giao hàng): Pending → Confirmed → Preparing → Delivering → Completed
     * Pickup (Nhận tại quán): Pending → Confirmed → Preparing → Completed
     *
     * @param self $status Trạng thái đích
     * @param OrderType|null $orderType Loại đơn hàng (delivery/pickup)
     */
    public function canTransitionTo(self $status, ?OrderType $orderType = null): bool
    {
        return match ($this) {
            self::Pending => in_array($status, [self::Confirmed, self::Cancelled]),
            self::Confirmed => in_array($status, [self::Preparing, self::Cancelled]),
            self::Preparing => match ($orderType) {
                OrderType::Pickup => $status === self::Completed,       // Pickup: pha xong → hoàn thành
                default          => $status === self::Delivering,       // Delivery: pha xong → giao hàng
            },
            self::Delivering => $status === self::Completed,
            self::Ready => in_array($status, [self::Delivering, self::Completed]), // Tương thích data cũ
            self::Completed, self::Cancelled => false,
        };
    }
}
