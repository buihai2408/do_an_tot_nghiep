<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __invoke()
    {
        $featuredProducts = Product::with(['category', 'sizes', 'approvedReviews', 'primaryImage'])
            ->active()
            ->featured()
            ->take(8)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'image' => $p->primaryImage?->path,
                'base_price' => $p->base_price,
                'category' => $p->category?->name,
                'avg_rating' => $p->avg_rating,
                'review_count' => $p->review_count,
            ]);

        $categories = Category::active()->orderBy('sort_order')->get();

        return Inertia::render('Home', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
        ]);
    }
}
