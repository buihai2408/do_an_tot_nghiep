<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Reviews/Index', [
            'reviews' => Review::with(['user', 'order'])->latest()->paginate(15),
        ]);
    }
}
