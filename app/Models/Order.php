<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_number', 'status', 'order_type',
        'subtotal', 'discount_amount', 'shipping_fee', 'total',
        'points_earned', 'points_used', 'points_discount',
        'coupon_id', 'payment_method', 'payment_status',
        'customer_name', 'customer_phone', 'customer_email',
        'shipping_address', 'note',
        'confirmed_at', 'completed_at', 'cancelled_at', 'cancel_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'order_type' => OrderType::class,
            'payment_method' => PaymentMethod::class,
            'payment_status' => PaymentStatus::class,
            'subtotal' => 'decimal:0',
            'discount_amount' => 'decimal:0',
            'shipping_fee' => 'decimal:0',
            'total' => 'decimal:0',
            'points_earned' => 'integer',
            'points_used' => 'integer',
            'points_discount' => 'decimal:0',
            'confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public static function generateOrderNumber(): string
    {
        return 'CF' . date('Ymd') . strtoupper(substr(uniqid(), -6));
    }
}
