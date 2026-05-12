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
        <!-- Page header -->
        <div class="mb-8">
            <p class="text-xs font-semibold tracking-[0.2em] uppercase mb-1 text-[#D4A853]">Tổng quan</p>
            <h1 class="text-2xl font-bold text-[#2C1810] font-playfair">Dashboard</h1>
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Revenue summary -->
            <div class="bg-white p-6 rounded-lg border border-[#E8D9C5] shadow-sm">
                <h2 class="text-xs font-bold tracking-widest uppercase mb-5 pb-3 text-[#2C1810] border-b border-[#F2EBE0]">📊 Tổng quan</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-[#F9F5F0]">
                        <span class="text-sm text-[#8B7355]">Tổng doanh thu</span>
                        <span class="font-bold text-sm text-[#2C1810]">{{ formatCurrency(stats.total_revenue) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-[#F9F5F0]">
                        <span class="text-sm text-[#8B7355]">Tổng đơn hàng</span>
                        <span class="font-bold text-sm text-[#2C1810]">{{ stats.total_orders }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-[#8B7355]">Tổng sản phẩm</span>
                        <span class="font-bold text-sm text-[#2C1810]">{{ stats.total_products }}</span>
                    </div>
                </div>
            </div>

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
                            <span class="text-sm font-medium text-[#2C1810]">{{ product.product_name }}</span>
                        </div>
                        <span class="text-sm font-semibold text-[#D4A853]">{{ product.total_sold }} ly</span>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
