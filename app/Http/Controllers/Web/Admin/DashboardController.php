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
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        return Inertia::render('Admin/Dashboard', [
            'stats' => $reportService->getDashboardStats($month, $year),
            'revenueChart' => $reportService->getRevenueReport('month', $month, $year),
            'topProducts' => $reportService->getTopProducts(10, $month, $year),
            'topCustomers' => $reportService->getTopCustomers(10, $month, $year),
            'ordersByStatus' => $reportService->getOrdersByStatus($month, $year),
            'month' => (int)$month,
            'year' => (int)$year,
        ]);
    }
}
