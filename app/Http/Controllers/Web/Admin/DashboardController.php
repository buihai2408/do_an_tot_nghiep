<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(ReportService $reportService)
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => $reportService->getDashboardStats(),
            'revenueChart' => $reportService->getRevenueReport('week'),
            'topProducts' => $reportService->getTopProducts(5),
            'ordersByStatus' => $reportService->getOrdersByStatus(),
        ]);
    }
}
