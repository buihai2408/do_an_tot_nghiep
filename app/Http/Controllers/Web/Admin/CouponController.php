<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Inertia\Inertia;

class CouponController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Coupons/Index', [
            'coupons' => Coupon::latest()->paginate(15),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Coupons/Form');
    }

    public function edit(Coupon $coupon)
    {
        return Inertia::render('Admin/Coupons/Form', [
            'coupon' => $coupon,
        ]);
    }
}
