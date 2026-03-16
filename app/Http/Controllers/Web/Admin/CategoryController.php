<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('sort_order')->paginate(20);
        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
        ]);
    }
}
