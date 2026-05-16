<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { useFormatters } from '@/Composables/useFormatters';

const { formatCurrency } = useFormatters();
const props = defineProps({ orders: Object, statuses: Array, filters: Object });

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');

const applyFilters = () => {
    router.get('/admin/orders', { search: search.value || undefined, status: statusFilter.value || undefined }, { preserveState: true });
};

const statusColors = {
    yellow: 'bg-yellow-100 text-yellow-800', blue: 'bg-blue-100 text-blue-800',
    indigo: 'bg-indigo-100 text-indigo-800', purple: 'bg-purple-100 text-purple-800',
    orange: 'bg-orange-100 text-orange-800', green: 'bg-green-100 text-green-800', red: 'bg-red-100 text-red-800',
};

onMounted(() => {
    if (window.Echo) {
        window.Echo.private('admin.orders')
            .listen('.NewOrderPlaced', () => {
                router.reload({ only: ['orders'] });
            });
    }
});

onUnmounted(() => {
    if (window.Echo) {
        // Not leaving the channel here because AdminLayout is also listening
        // and we want AdminLayout to keep receiving notifications
    }
});
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-bold text-[#1a1a1a] font-serif mb-6">Quản lý đơn hàng</h1>

        <div class="bg-white rounded border border-[#E8D9C5] p-4 mb-6 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input v-model="search" @keyup.enter="applyFilters" placeholder="Tìm mã đơn, tên, SĐT..." class="rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]" />
                <select v-model="statusFilter" @change="applyFilters" class="rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]">
                    <option value="">Tất cả trạng thái</option>
                    <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                </select>
            </div>
        </div>

        <div class="bg-white rounded border border-[#E8D9C5] overflow-hidden shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-[#FAF6F0]">
                    <tr>
                        <th class="px-6 py-3 text-left text-gray-500">Mã đơn</th>
                        <th class="px-6 py-3 text-left text-gray-500">Khách hàng</th>
                        <th class="px-6 py-3 text-center text-gray-500">Trạng thái</th>
                        <th class="px-6 py-3 text-right text-gray-500">Tổng tiền</th>
                        <th class="px-6 py-3 text-right text-gray-500">Ngày tạo</th>
                        <th class="px-6 py-3 text-right text-gray-500">Chi tiết</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="order in orders.data" :key="order.id" class="hover:bg-[#FAF6F0] transition">
                        <td class="px-6 py-4 font-medium">#{{ order.order_number }}</td>
                        <td class="px-6 py-4">{{ order.customer_name }}</td>
                        <td class="px-6 py-4 text-center">
                            <span :class="statusColors[order.status_color]" class="px-2 py-1 rounded-full text-xs font-semibold">{{ order.status_label }}</span>
                        </td>
                        <td class="px-6 py-4 text-right font-medium">{{ formatCurrency(order.total) }}</td>
                        <td class="px-6 py-4 text-right text-gray-500">{{ order.created_at }}</td>
                        <td class="px-6 py-4 text-right">
                            <Link :href="`/admin/orders/${order.id}`" class="text-[#D4A853] hover:text-[#2C1810] transition font-medium">Xem</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <Pagination :links="orders.links" />
    </AdminLayout>
</template>
