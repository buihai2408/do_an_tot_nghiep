<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function revenue(Request $request, ReportService $reportService)
    {
        return response()->json($reportService->getRevenueReport($request->get('period', 'week')));
    }

    public function orders(ReportService $reportService)
    {
        return response()->json($reportService->getOrdersByStatus());
    }

    public function products(ReportService $reportService)
    {
        return response()->json($reportService->getTopProducts());
    }

    public function customers(ReportService $reportService)
    {
        return response()->json($reportService->getTopCustomers());
    }
}
