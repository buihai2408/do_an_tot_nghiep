<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\MenuController;
use App\Http\Controllers\Web\CartPageController;
use App\Http\Controllers\Web\CheckoutPageController;
use App\Http\Controllers\Web\OrderPageController;
use App\Http\Controllers\Web\LoyaltyController;
use App\Http\Controllers\Api\PayOSController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Web\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Web\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Web\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Web\Admin\ToppingController as AdminToppingController;
use App\Http\Controllers\Web\Admin\SizeController as AdminSizeController;
use App\Http\Controllers\Web\Admin\UserController as AdminUserController;
use App\Http\Controllers\Web\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Web\Admin\ReportController as AdminReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{slug}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/cart', CartPageController::class)->name('cart.index');
Route::get('/about', fn () => inertia('About'))->name('about');
Route::get('/contact', fn () => inertia('Contact'))->name('contact');

Route::get('/checkout/payos-return', [PayOSController::class, 'return'])->name('checkout.payos.return');
Route::get('/checkout/payos-cancel', [PayOSController::class, 'cancel'])->name('checkout.payos.cancel');

Route::middleware('auth')->group(function () {
    Route::get('/checkout', CheckoutPageController::class)->name('checkout.index');
    Route::get('/orders', [OrderPageController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderPageController::class, 'show'])->name('orders.show');
    Route::get('/loyalty', [LoyaltyController::class, 'index'])->name('loyalty.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', \App\Http\Middleware\EnsureIsAdminOrStaff::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
    Route::get('/coupons/create', [AdminCouponController::class, 'create'])->name('coupons.create');
    Route::get('/coupons/{coupon}/edit', [AdminCouponController::class, 'edit'])->name('coupons.edit');
    Route::get('/toppings', [AdminToppingController::class, 'index'])->name('toppings.index');
    Route::get('/sizes', [AdminSizeController::class, 'index'])->name('sizes.index');
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reports', AdminReportController::class)->name('reports.index');

    // Admin-only
    Route::middleware(\App\Http\Middleware\EnsureIsAdmin::class)->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    });
});

require __DIR__.'/auth.php';
