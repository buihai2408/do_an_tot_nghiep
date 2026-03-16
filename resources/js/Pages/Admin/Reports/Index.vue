<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { useFormatters } from '@/Composables/useFormatters';

const { formatCurrency } = useFormatters();

defineProps({
    revenueChart: Object,
    topProducts: Array,
    topCustomers: Array,
    ordersByStatus: Object,
    period: String,
});

const changePeriod = (p) => router.get('/admin/reports', { period: p }, { preserveState: true });
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Báo cáo</h1>

        <div class="flex space-x-2 mb-6">
            <button v-for="p in ['week', 'month', 'year']" :key="p" @click="changePeriod(p)"
                :class="period === p ? 'bg-amber-700 text-white' : 'bg-white text-gray-700'"
                class="px-4 py-2 rounded-lg font-medium transition">
                {{ p === 'week' ? '7 ngày' : p === 'month' ? '30 ngày' : '1 năm' }}
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Revenue table -->
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h2 class="font-bold mb-4">Doanh thu theo ngày</h2>
                <div v-if="revenueChart.labels?.length === 0" class="text-gray-500 text-center py-8">Chưa có dữ liệu</div>
                <table v-else class="w-full text-sm">
                    <thead><tr><th class="text-left pb-2">Ngày</th><th class="text-right pb-2">Doanh thu</th><th class="text-right pb-2">Đơn hàng</th></tr></thead>
                    <tbody>
                        <tr v-for="(label, i) in revenueChart.labels" :key="label" class="border-t">
                            <td class="py-2">{{ label }}</td>
                            <td class="py-2 text-right font-medium">{{ formatCurrency(revenueChart.revenue[i]) }}</td>
                            <td class="py-2 text-right">{{ revenueChart.orders[i] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Orders by status -->
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h2 class="font-bold mb-4">Đơn hàng theo trạng thái</h2>
                <div class="space-y-3">
                    <div v-for="(count, status) in ordersByStatus" :key="status" class="flex justify-between items-center">
                        <span class="capitalize">{{ status }}</span>
                        <span class="font-bold bg-amber-100 text-amber-700 px-3 py-1 rounded-full">{{ count }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h2 class="font-bold mb-4">Top sản phẩm bán chạy</h2>
                <div class="space-y-3">
                    <div v-for="(p, i) in topProducts" :key="i" class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <span class="w-6 h-6 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center text-xs font-bold">{{ i + 1 }}</span>
                            <span>{{ p.product_name }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-medium">{{ p.total_sold }} ly</span>
                            <span class="text-gray-500 ml-2">{{ formatCurrency(p.total_revenue) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-6">
                <h2 class="font-bold mb-4">Top khách hàng</h2>
                <div class="space-y-3">
                    <div v-for="(c, i) in topCustomers" :key="i" class="flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            <span class="w-6 h-6 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center text-xs font-bold">{{ i + 1 }}</span>
                            <div><p class="font-medium">{{ c.name }}</p><p class="text-xs text-gray-500">{{ c.orders_count }} đơn</p></div>
                        </div>
                        <span class="font-medium text-amber-700">{{ formatCurrency(c.total_spent) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
