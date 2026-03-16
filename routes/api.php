<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Api\Admin\SizeController as AdminSizeController;
use App\Http\Controllers\Api\Admin\ToppingController as AdminToppingController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Api\Admin\ReportController as AdminReportController;
use Illuminate\Support\Facades\Route;

// Cart (public - works with session for guests)
Route::post('/cart/items', [CartController::class, 'store']);
Route::put('/cart/items/{cartItem}', [CartController::class, 'update']);
Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroy']);
Route::delete('/cart', [CartController::class, 'clear']);

// Auth-required routes
Route::middleware('auth:sanctum')->group(function () {
    // Checkout
    Route::post('/checkout', [CheckoutController::class, 'store']);
    Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon']);

    // Orders
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::post('/orders/{order}/review', [OrderController::class, 'review']);

    // Addresses
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::put('/addresses/{address}', [AddressController::class, 'update']);
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy']);

    // Admin API (Admin + Staff)
    Route::middleware(\App\Http\Middleware\EnsureIsAdminOrStaff::class)->prefix('admin')->group(function () {
        Route::get('/orders/new', [AdminOrderController::class, 'newOrders']);

        Route::post('/categories', [AdminCategoryController::class, 'store']);
        Route::post('/categories/{category}', [AdminCategoryController::class, 'update']);
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy']);

        Route::post('/products', [AdminProductController::class, 'store']);
        Route::post('/products/{product}', [AdminProductController::class, 'update']);
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy']);

        Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus']);

        Route::post('/coupons', [AdminCouponController::class, 'store']);
        Route::put('/coupons/{coupon}', [AdminCouponController::class, 'update']);
        Route::delete('/coupons/{coupon}', [AdminCouponController::class, 'destroy']);

        Route::post('/sizes', [AdminSizeController::class, 'store']);
        Route::put('/sizes/{size}', [AdminSizeController::class, 'update']);
        Route::delete('/sizes/{size}', [AdminSizeController::class, 'destroy']);

        Route::post('/toppings', [AdminToppingController::class, 'store']);
        Route::put('/toppings/{topping}', [AdminToppingController::class, 'update']);
        Route::delete('/toppings/{topping}', [AdminToppingController::class, 'destroy']);

        Route::put('/reviews/{review}/approve', [AdminReviewController::class, 'approve']);
        Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy']);

        Route::get('/reports/revenue', [AdminReportController::class, 'revenue']);
        Route::get('/reports/orders', [AdminReportController::class, 'orders']);
        Route::get('/reports/products', [AdminReportController::class, 'products']);
        Route::get('/reports/customers', [AdminReportController::class, 'customers']);

        // Admin-only
        Route::middleware(\App\Http\Middleware\EnsureIsAdmin::class)->group(function () {
            Route::put('/users/{user}', [AdminUserController::class, 'update']);
        });
    });
});
