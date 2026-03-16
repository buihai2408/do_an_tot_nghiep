<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemTopping extends Model
{
    public $timestamps = false;

    protected $table = 'order_item_topping';

    protected $fillable = ['order_item_id', 'topping_name', 'price'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:0',
        ];
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}
