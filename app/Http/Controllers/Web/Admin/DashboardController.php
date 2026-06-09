<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, ReportService $reportService)
    {
        $period = $request->get('period', 'week');

        return Inertia::render('Admin/Dashboard', [
            'stats' => $reportService->getDashboardStats(),
            'revenueChart' => $reportService->getRevenueReport($period),
            'topProducts' => $reportService->getTopProducts(10),
            'topCustomers' => $reportService->getTopCustomers(10),
            'ordersByStatus' => $reportService->getOrdersByStatus(),
            'period' => $period,
        ]);
    }
}
