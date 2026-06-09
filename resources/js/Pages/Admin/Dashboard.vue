<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useFormatters } from '@/Composables/useFormatters';
import { computed, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { Bar, Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

const { formatCurrency } = useFormatters();

const props = defineProps({
    stats: Object,
    revenueChart: Object,
    topProducts: Array,
    topCustomers: Array,
    ordersByStatus: Object,
    period: {
        type: String,
        default: 'week'
    }
});

const changePeriod = (p) => router.get('/admin', { period: p }, { preserveState: true });

const revenueChartData = computed(() => {
    return {
        labels: props.revenueChart?.labels || [],
        datasets: [
            {
                label: 'Doanh thu (VNĐ)',
                backgroundColor: '#D4A853',
                borderRadius: 4,
                data: props.revenueChart?.revenue || []
            }
        ]
    };
});

const revenueChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: (value) => new Intl.NumberFormat('vi-VN').format(value)
            }
        }
    }
};

const getStatusLabel = (status) => {
    const map = {
        'pending': 'Chờ xác nhận',
        'processing': 'Đang chuẩn bị',
        'delivering': 'Đang giao',
        'completed': 'Hoàn thành',
        'cancelled': 'Đã hủy',
    };
    return map[status] || status;
};

const orderStatusChartData = computed(() => {
    const keys = Object.keys(props.ordersByStatus || {});
    return {
        labels: keys.map(k => getStatusLabel(k)),
        datasets: [
            {
                backgroundColor: ['#F59E0B', '#3B82F6', '#8B5CF6', '#10B981', '#EF4444'],
                data: Object.values(props.ordersByStatus || {})
            }
        ]
    };
});

const orderStatusChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'right' }
    }
};

onMounted(() => {
    if (window.Echo) {
        window.Echo.private('admin.orders')
            .listen('.NewOrderPlaced', () => {
                router.reload({ only: ['stats', 'revenueChart', 'ordersByStatus'] });
            });
    }
});

onUnmounted(() => {
    if (window.Echo) {
        // Not leaving channel to allow AdminLayout to keep listening
    }
});
</script>

<template>
    <AdminLayout>
        <!-- Page header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold tracking-[0.2em] uppercase mb-1 text-[#D4A853]">Tổng quan</p>
                <h1 class="text-2xl font-bold text-[#2C1810] font-playfair">Dashboard</h1>
            </div>
            
            <!-- Period Selector -->
            <div class="flex space-x-2 bg-white p-1 rounded-lg border border-[#E8D9C5] shadow-sm">
                <button v-for="p in ['week', 'month', 'year']" :key="p" @click="changePeriod(p)"
                    :class="period === p ? 'bg-[#D4A853] text-[#1C1208] shadow' : 'text-[#8B7355] hover:bg-[#FAF6F0]'"
                    class="px-4 py-1.5 rounded-md text-sm font-semibold transition">
                    {{ p === 'week' ? '7 ngày' : p === 'month' ? '30 ngày' : '1 năm' }}
                </button>
            </div>
        </div>

        <!-- KPIs -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <!-- Doanh thu hôm nay -->
            <div class="bg-white p-6 rounded-lg border border-[#E8D9C5] border-l-4 border-l-[#D4A853] shadow-sm transition hover:-translate-y-0.5">
                <p class="text-xs font-semibold tracking-widest uppercase mb-2 text-[#8B7355]">💰 Doanh thu hôm nay</p>
                <p class="text-2xl font-bold text-[#2C1810] font-playfair">{{ formatCurrency(stats.revenue_today) }}</p>
            </div>
            <!-- Đơn hàng hôm nay -->
            <div class="bg-white p-6 rounded-lg border border-[#E8D9C5] border-l-4 border-l-[#5C3A1E] shadow-sm transition hover:-translate-y-0.5">
                <p class="text-xs font-semibold tracking-widest uppercase mb-2 text-[#8B7355]">📋 Đơn hàng hôm nay</p>
                <p class="text-2xl font-bold text-[#2C1810] font-playfair">{{ stats.orders_today }}</p>
            </div>
            <!-- Chờ xác nhận — nổi bật -->
            <div class="bg-white p-6 rounded-lg border border-[#FEF3C7] border-l-4 border-l-[#F59E0B] shadow-sm bg-amber-50/30 transition hover:-translate-y-0.5">
                <p class="text-xs font-semibold tracking-widest uppercase mb-2 text-[#92400E]">⏳ Chờ xác nhận</p>
                <p class="text-2xl font-bold text-[#D97706] font-playfair">{{ stats.pending_orders }}</p>
            </div>
            <!-- Tổng khách hàng -->
            <div class="bg-white p-6 rounded-lg border border-[#E8D9C5] border-l-4 border-l-[#8B7355] shadow-sm transition hover:-translate-y-0.5">
                <p class="text-xs font-semibold tracking-widest uppercase mb-2 text-[#8B7355]">👥 Tổng khách hàng</p>
                <p class="text-2xl font-bold text-[#2C1810] font-playfair">{{ stats.total_customers }}</p>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Revenue Chart -->
            <div class="bg-white p-6 rounded-lg border border-[#E8D9C5] shadow-sm lg:col-span-2">
                <h2 class="text-xs font-bold tracking-widest uppercase mb-5 pb-3 text-[#2C1810] border-b border-[#F2EBE0]">📈 Doanh thu 7 ngày gần nhất</h2>
                <div class="h-72 w-full relative">
                    <Bar v-if="revenueChartData.labels.length" :data="revenueChartData" :options="revenueChartOptions" />
                    <div v-else class="absolute inset-0 flex items-center justify-center text-[#B5A089]">Chưa có dữ liệu</div>
                </div>
            </div>

            <!-- Order Status Chart -->
            <div class="bg-white p-6 rounded-lg border border-[#E8D9C5] shadow-sm">
                <h2 class="text-xs font-bold tracking-widest uppercase mb-5 pb-3 text-[#2C1810] border-b border-[#F2EBE0]">📊 Trạng thái đơn hàng</h2>
                <div class="h-72 w-full relative">
                    <Doughnut v-if="orderStatusChartData.labels.length" :data="orderStatusChartData" :options="orderStatusChartOptions" />
                    <div v-else class="absolute inset-0 flex items-center justify-center text-[#B5A089]">Chưa có dữ liệu</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top products -->
            <div class="bg-white p-6 rounded-lg border border-[#E8D9C5] shadow-sm">
                <h2 class="text-xs font-bold tracking-widest uppercase mb-5 pb-3 text-[#2C1810] border-b border-[#F2EBE0]">☕ Sản phẩm bán chạy</h2>
                <div v-if="!topProducts?.length" class="text-center py-8 text-[#B5A089]">Chưa có dữ liệu</div>
                <div v-else class="space-y-3">
                    <div v-for="(product, index) in topProducts" :key="index" class="flex items-center justify-between py-2 border-b border-[#F9F5F0] last:border-0">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                                :class="index === 0 ? 'bg-[#D4A853] text-[#1C1208]' : index === 1 ? 'bg-[#E8D9C5] text-[#5C3A1E]' : index === 2 ? 'bg-[#F2EBE0] text-[#8B7355]' : 'bg-[#F9F5F0] text-[#B5A089]'">
                                {{ index + 1 }}
                            </span>
                            <div>
                                <p class="text-sm font-medium text-[#2C1810]">{{ product.product_name }}</p>
                                <p class="text-xs text-[#8B7355]">{{ formatCurrency(product.total_revenue) }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-[#D4A853]">{{ product.total_sold }} ly</span>
                    </div>
                </div>
            </div>

            <!-- Top customers -->
            <div class="bg-white p-6 rounded-lg border border-[#E8D9C5] shadow-sm">
                <h2 class="text-xs font-bold tracking-widest uppercase mb-5 pb-3 text-[#2C1810] border-b border-[#F2EBE0]">🌟 Top khách hàng</h2>
                <div v-if="!topCustomers?.length" class="text-center py-8 text-[#B5A089]">Chưa có dữ liệu</div>
                <div v-else class="space-y-3">
                    <div v-for="(customer, index) in topCustomers" :key="index" class="flex items-center justify-between py-2 border-b border-[#F9F5F0] last:border-0">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                                :class="index === 0 ? 'bg-[#D4A853] text-[#1C1208]' : index === 1 ? 'bg-[#E8D9C5] text-[#5C3A1E]' : index === 2 ? 'bg-[#F2EBE0] text-[#8B7355]' : 'bg-[#F9F5F0] text-[#B5A089]'">
                                {{ index + 1 }}
                            </span>
                            <div>
                                <p class="text-sm font-medium text-[#2C1810]">{{ customer.name }}</p>
                                <p class="text-xs text-[#8B7355]">{{ customer.orders_count }} đơn hàng</p>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-[#D4A853]">{{ formatCurrency(customer.total_spent) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
            <!-- Revenue summary -->
            <div class="bg-white p-6 rounded-lg border border-[#E8D9C5] shadow-sm">
                <h2 class="text-xs font-bold tracking-widest uppercase mb-5 pb-3 text-[#2C1810] border-b border-[#F2EBE0]">📋 Tổng kết hoạt động</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-[#F9F5F0]">
                        <span class="text-sm text-[#8B7355]">Tổng doanh thu tích lũy</span>
                        <span class="font-bold text-sm text-[#2C1810]">{{ formatCurrency(stats.total_revenue) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-[#F9F5F0]">
                        <span class="text-sm text-[#8B7355]">Tổng đơn hàng đã xử lý</span>
                        <span class="font-bold text-sm text-[#2C1810]">{{ stats.total_orders }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-[#F9F5F0]">
                        <span class="text-sm text-[#8B7355]">Tổng số sản phẩm trong Menu</span>
                        <span class="font-bold text-sm text-[#2C1810]">{{ stats.total_products }}</span>
                    </div>
                </div>
            </div>

            <!-- Revenue Table -->
            <div class="bg-white p-6 rounded-lg border border-[#E8D9C5] shadow-sm">
                <h2 class="text-xs font-bold tracking-widest uppercase mb-5 pb-3 text-[#2C1810] border-b border-[#F2EBE0]">📅 Doanh thu theo ngày</h2>
                <div v-if="!revenueChart?.labels?.length" class="text-center py-8 text-[#B5A089]">Chưa có dữ liệu</div>
                <div v-else class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr>
                                <th class="text-left pb-3 font-semibold text-[#8B7355] border-b border-[#F9F5F0]">Ngày</th>
                                <th class="text-right pb-3 font-semibold text-[#8B7355] border-b border-[#F9F5F0]">Đơn hàng</th>
                                <th class="text-right pb-3 font-semibold text-[#8B7355] border-b border-[#F9F5F0]">Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(label, i) in revenueChart.labels" :key="label" class="border-b border-[#F9F5F0] last:border-0 hover:bg-[#FAF6F0] transition">
                                <td class="py-3 text-[#2C1810]">{{ label }}</td>
                                <td class="py-3 text-right text-[#2C1810]">{{ revenueChart.orders[i] }}</td>
                                <td class="py-3 text-right font-medium text-[#D4A853]">{{ formatCurrency(revenueChart.revenue[i]) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
