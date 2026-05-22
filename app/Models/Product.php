<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description',
        'base_price', 'is_active', 'is_featured',
        'has_ice_level', 'has_sugar_level',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'has_ice_level' => 'boolean',
            'has_sugar_level' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class, 'product_size')
            ->withPivot('price');
    }

    public function toppings(): BelongsToMany
    {
        return $this->belongsToMany(Topping::class, 'product_topping');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function orderBasedReviews()
    {
        return Review::where('is_approved', true)
            ->where(function ($q) {
                $q->where('product_id', $this->id)
                  ->orWhereIn('order_id', fn($sub) =>
                      $sub->select('order_id')
                          ->from('order_items')
                          ->where('product_id', $this->id)
                  );
            });
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getAvgRatingAttribute(): float
    {
        return round($this->orderBasedReviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewCountAttribute(): int
    {
        return $this->orderBasedReviews()->count();
    }
}
