<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Inertia\Inertia;

class SizeController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Sizes/Index', [
            'sizes' => Size::orderBy('sort_order')->get(),
        ]);
    }
}
