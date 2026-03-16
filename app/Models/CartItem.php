<?php

namespace App\Models;

use App\Enums\IceLevel;
use App\Enums\SugarLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id', 'product_id', 'size_id',
        'ice_level', 'sugar_level', 'quantity', 'unit_price',
    ];

    protected function casts(): array
    {
        return [
            'ice_level' => IceLevel::class,
            'sugar_level' => SugarLevel::class,
            'quantity' => 'integer',
            'unit_price' => 'decimal:0',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    public function toppings(): BelongsToMany
    {
        return $this->belongsToMany(Topping::class, 'cart_item_topping')
            ->withPivot('price');
    }

    public function getTotalAttribute(): float
    {
        $toppingTotal = $this->toppings->sum('pivot.price');
        return ($this->unit_price + $toppingTotal) * $this->quantity;
    }
}
