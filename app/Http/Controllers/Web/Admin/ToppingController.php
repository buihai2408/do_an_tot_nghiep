<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topping;
use Inertia\Inertia;

class ToppingController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Toppings/Index', [
            'toppings' => Topping::latest()->paginate(20),
        ]);
    }
}
