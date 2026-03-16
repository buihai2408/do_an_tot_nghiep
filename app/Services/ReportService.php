<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getDashboardStats(): array
    {
        $today = now()->startOfDay();

        return [
            'revenue_today' => Order::where('status', OrderStatus::Completed)
                ->where('completed_at', '>=', $today)
                ->sum('total'),
            'orders_today' => Order::where('created_at', '>=', $today)->count(),
            'new_customers' => User::where('role', 'customer')
                ->where('created_at', '>=', $today)->count(),
            'pending_orders' => Order::where('status', OrderStatus::Pending)->count(),
            'total_revenue' => Order::where('status', OrderStatus::Completed)->sum('total'),
            'total_orders' => Order::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_products' => Product::count(),
        ];
    }

    public function getRevenueReport(string $period = 'week'): array
    {
        $days = match ($period) {
            'week' => 7,
            'month' => 30,
            'year' => 365,
            default => 7,
        };

        $startDate = now()->subDays($days)->startOfDay();

        $data = Order::where('status', OrderStatus::Completed)
            ->where('completed_at', '>=', $startDate)
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

    public function getTopProducts(int $limit = 10): array
    {
        return OrderItem::select(
            'product_name',
            DB::raw('SUM(quantity) as total_sold'),
            DB::raw('SUM(subtotal) as total_revenue')
        )
            ->whereHas('order', fn($q) => $q->where('status', OrderStatus::Completed))
            ->groupBy('product_name')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getOrdersByStatus(): array
    {
        return Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($item) => [$item->status->value => $item->count])
            ->toArray();
    }

    public function getTopCustomers(int $limit = 10): array
    {
        return User::where('role', 'customer')
            ->withCount(['orders' => fn($q) => $q->where('status', OrderStatus::Completed)])
            ->withSum(['orders' => fn($q) => $q->where('status', OrderStatus::Completed)], 'total')
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
