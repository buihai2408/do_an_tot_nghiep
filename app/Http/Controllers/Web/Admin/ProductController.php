<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Size;
use App\Models\Topping;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'primaryImage']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        return Inertia::render('Admin/Products/Index', [
            'products' => $query->latest()->paginate(15),
            'categories' => Category::orderBy('sort_order')->get(),
            'filters' => $request->only(['search', 'category']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Products/Create', [
            'categories' => Category::active()->orderBy('sort_order')->get(),
            'sizes' => Size::orderBy('sort_order')->get(),
            'toppings' => Topping::active()->get(),
        ]);
    }

    public function edit(Product $product)
    {
        $product->load(['sizes', 'toppings', 'images']);

        return Inertia::render('Admin/Products/Edit', [
            'product' => $product,
            'categories' => Category::active()->orderBy('sort_order')->get(),
            'sizes' => Size::orderBy('sort_order')->get(),
            'toppings' => Topping::active()->get(),
        ]);
    }
}
