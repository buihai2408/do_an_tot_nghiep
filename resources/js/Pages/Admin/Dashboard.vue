<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useFormatters } from '@/Composables/useFormatters';

const { formatCurrency } = useFormatters();

defineProps({
    stats: Object,
    revenueChart: Object,
    topProducts: Array,
    ordersByStatus: Object,
});
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

        <!-- KPIs -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 shadow-md">
                <p class="text-sm text-gray-500">Doanh thu hôm nay</p>
                <p class="text-2xl font-bold text-amber-700 mt-1">{{ formatCurrency(stats.revenue_today) }}</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-md">
                <p class="text-sm text-gray-500">Đơn hàng hôm nay</p>
                <p class="text-2xl font-bold text-blue-700 mt-1">{{ stats.orders_today }}</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-md">
                <p class="text-sm text-gray-500">Đơn chờ xác nhận</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">{{ stats.pending_orders }}</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-md">
                <p class="text-sm text-gray-500">Tổng khách hàng</p>
                <p class="text-2xl font-bold text-green-700 mt-1">{{ stats.total_customers }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Revenue summary -->
            <div class="bg-white rounded-2xl p-6 shadow-md">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Tổng quan</h2>
                <div class="space-y-4">
                    <div class="flex justify-between"><span class="text-gray-600">Tổng doanh thu</span><span class="font-bold text-amber-700">{{ formatCurrency(stats.total_revenue) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-600">Tổng đơn hàng</span><span class="font-bold">{{ stats.total_orders }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-600">Tổng sản phẩm</span><span class="font-bold">{{ stats.total_products }}</span></div>
                </div>
            </div>

            <!-- Top products -->
            <div class="bg-white rounded-2xl p-6 shadow-md">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Sản phẩm bán chạy</h2>
                <div v-if="topProducts.length === 0" class="text-gray-500 text-center py-8">Chưa có dữ liệu</div>
                <div v-else class="space-y-3">
                    <div v-for="(product, index) in topProducts" :key="index" class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="w-6 h-6 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center text-xs font-bold">{{ index + 1 }}</span>
                            <span class="text-sm">{{ product.product_name }}</span>
                        </div>
                        <span class="text-sm font-medium text-gray-600">{{ product.total_sold }} ly</span>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
