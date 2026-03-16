<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Enums\IceLevel;
use App\Enums\SugarLevel;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'sizes', 'primaryImage'])
            ->active();

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('sort')) {
            match ($request->sort) {
                'price_asc' => $query->orderBy('base_price', 'asc'),
                'price_desc' => $query->orderBy('base_price', 'desc'),
                'name' => $query->orderBy('name', 'asc'),
                default => $query->latest(),
            };
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->through(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'slug' => $p->slug,
            'image' => $p->primaryImage?->path ?? $p->image,
            'base_price' => $p->base_price,
            'category' => $p->category?->name,
            'avg_rating' => $p->avg_rating,
            'review_count' => $p->review_count,
            'sizes' => $p->sizes->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'label' => $s->label,
                'price' => $s->pivot->price,
            ]),
        ]);

        $categories = Category::active()->orderBy('sort_order')->get();

        return Inertia::render('Menu/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['category', 'search', 'sort']),
        ]);
    }

    public function show(string $slug)
    {
        $product = Product::with(['category', 'sizes', 'toppings' => fn($q) => $q->active(), 'approvedReviews.user', 'images'])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        return Inertia::render('Menu/Show', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'image' => $product->primaryImage?->path ?? $product->image,
                'images' => $product->images->map(fn($img) => [
                    'id' => $img->id,
                    'path' => $img->path,
                    'is_primary' => $img->is_primary,
                ]),
                'base_price' => $product->base_price,
                'category' => $product->category,
                'sizes' => $product->sizes->map(fn($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'label' => $s->label,
                    'price' => $s->pivot->price,
                ]),
                'toppings' => $product->toppings->map(fn($t) => [
                    'id' => $t->id,
                    'name' => $t->name,
                    'price' => $t->price,
                ]),
                'avg_rating' => $product->avg_rating,
                'review_count' => $product->review_count,
                'reviews' => $product->approvedReviews->take(10)->map(fn($r) => [
                    'id' => $r->id,
                    'user_name' => $r->user->name,
                    'rating' => $r->rating,
                    'comment' => $r->comment,
                    'created_at' => $r->created_at->diffForHumans(),
                ]),
            ],
            'ice_levels' => collect(IceLevel::cases())->map(fn($l) => ['value' => $l->value, 'label' => $l->label()]),
            'sugar_levels' => collect(SugarLevel::cases())->map(fn($l) => ['value' => $l->value, 'label' => $l->label()]),
        ]);
    }
}
