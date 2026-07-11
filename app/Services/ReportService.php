<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    public function getDashboardStats(?int $month = null, ?int $year = null): array
    {
        if ($month && $year) {
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = Carbon::create($year, $month, 1)->endOfMonth();
        } else {
            $start = now()->startOfDay();
            $end = now()->endOfDay();
        }

        return [
            'revenue_today' => Order::where('status', OrderStatus::Completed)
                ->whereBetween('completed_at', [$start, $end])
                ->sum('total'),
            'orders_today' => Order::whereBetween('created_at', [$start, $end])->count(),
            'new_customers' => User::where('role', 'customer')
                ->whereBetween('created_at', [$start, $end])->count(),
            'pending_orders' => Order::where('status', OrderStatus::Pending)->count(), 
            'total_revenue' => Order::where('status', OrderStatus::Completed)->sum('total'),
            'total_orders' => Order::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_products' => Product::count(),
        ];
    }

    public function getRevenueReport(string $period = 'week', ?int $month = null, ?int $year = null): array
    {
        if ($month && $year) {
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        } else {
            $days = match ($period) {
                'week' => 7,
                'month' => 30,
                'year' => 365,
                default => 7,
            };
            $startDate = now()->subDays($days)->startOfDay();
            $endDate = now()->endOfDay();
        }

        $data = Order::where('status', OrderStatus::Completed)
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(completed_at) as date'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->toArray(),
            'revenue' => $data->pluck('revenue')->toArray(),
            'orders' => $data->pluck('orders_count')->toArray(),
        ];
    }

    public function getTopProducts(int $limit = 10, ?int $month = null, ?int $year = null): array
    {
        return OrderItem::select(
            'product_name',
            DB::raw('SUM(quantity) as total_sold'),
            DB::raw('SUM(subtotal) as total_revenue')
        )
            ->whereHas('order', function ($q) use ($month, $year) {
                $q->where('status', OrderStatus::Completed);
                if ($month && $year) {
                    $start = Carbon::create($year, $month, 1)->startOfMonth();
                    $end = Carbon::create($year, $month, 1)->endOfMonth();
                    $q->whereBetween('completed_at', [$start, $end]);
                }
            })
            ->groupBy('product_name')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getOrdersByStatus(?int $month = null, ?int $year = null): array
    {
        $query = Order::query();
        if ($month && $year) {
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = Carbon::create($year, $month, 1)->endOfMonth();
            $query->whereBetween('created_at', [$start, $end]);
        }

        return $query->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($item) => [$item->status->value => $item->count])
            ->toArray();
    }

    public function getTopCustomers(int $limit = 10, ?int $month = null, ?int $year = null): array
    {
        return User::where('role', 'customer')
            ->withCount(['orders' => function ($q) use ($month, $year) {
                $q->where('status', OrderStatus::Completed);
                if ($month && $year) {
                    $start = Carbon::create($year, $month, 1)->startOfMonth();
                    $end = Carbon::create($year, $month, 1)->endOfMonth();
                    $q->whereBetween('completed_at', [$start, $end]);
                }
            }])
            ->withSum(['orders' => function ($q) use ($month, $year) {
                $q->where('status', OrderStatus::Completed);
                if ($month && $year) {
                    $start = Carbon::create($year, $month, 1)->startOfMonth();
                    $end = Carbon::create($year, $month, 1)->endOfMonth();
                    $q->whereBetween('completed_at', [$start, $end]);
                }
            }], 'total')
            ->orderByDesc('orders_sum_total')
            ->limit($limit)
            ->get()
            ->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'orders_count' => $u->orders_count,
                'total_spent' => $u->orders_sum_total ?? 0,
            ])
            ->toArray();
    }
}
