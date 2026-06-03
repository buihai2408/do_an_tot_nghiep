<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useFormatters } from '@/Composables/useFormatters';
import axios from 'axios';

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

// ── Quick Action ──────────────────────────────────────────
const quickLoading = ref({});

const quickConfirm = async (order) => {
    quickLoading.value[order.id] = true;
    try {
        await axios.put(`/api/admin/orders/${order.id}/status`, { status: 'confirmed' });
        router.reload({ only: ['orders'] });
    } catch (e) {
        alert(e.response?.data?.message || 'Không thể xác nhận đơn hàng');
    } finally {
        quickLoading.value[order.id] = false;
    }
};

// ── Bulk Action ───────────────────────────────────────────
const selectedIds = ref([]);
const bulkLoading = ref(false);
const bulkMessage = ref('');

const isAllSelected = computed(() => {
    const selectableOrders = props.orders.data.filter(o => o.status === 'pending');
    return selectableOrders.length > 0 && selectableOrders.every(o => selectedIds.value.includes(o.id));
});

const toggleAll = () => {
    const selectableOrders = props.orders.data.filter(o => o.status === 'pending');
    if (isAllSelected.value) {
        selectedIds.value = [];
    } else {
        selectedIds.value = selectableOrders.map(o => o.id);
    }
};

const clearSelection = () => {
    selectedIds.value = [];
    bulkMessage.value = '';
};

const bulkConfirm = async () => {
    if (!selectedIds.value.length) return;
    bulkLoading.value = true;
    bulkMessage.value = '';
    try {
        const { data } = await axios.post('/api/admin/orders/bulk-status', {
            order_ids: selectedIds.value,
            status: 'confirmed',
        });
        bulkMessage.value = data.message;
        selectedIds.value = [];
        router.reload({ only: ['orders'] });
    } catch (e) {
        bulkMessage.value = e.response?.data?.message || 'Có lỗi xảy ra';
    } finally {
        bulkLoading.value = false;
    }
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

        <!-- ── Bulk Action Bar ─────────────────────────────────── -->
        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-2"
        >
            <div v-if="selectedIds.length" class="bg-[#2C1810] text-white rounded-lg p-4 mb-4 flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-3">
                    <span class="bg-[#D4A853] text-[#2C1810] font-bold px-3 py-1 rounded-full text-sm">{{ selectedIds.length }}</span>
                    <span class="font-medium">đơn hàng đã chọn</span>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        @click="bulkConfirm"
                        :disabled="bulkLoading"
                        class="flex items-center gap-2 px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded font-semibold text-sm transition disabled:opacity-50"
                    >
                        <svg v-if="bulkLoading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ bulkLoading ? 'Đang xử lý...' : 'Xác nhận tất cả' }}
                    </button>
                    <button @click="clearSelection" class="px-4 py-2 border border-white/30 hover:bg-white/10 text-white rounded text-sm transition">
                        Bỏ chọn
                    </button>
                </div>
            </div>
        </transition>

        <!-- ── Bulk result message ─────────────────────────────── -->
        <div v-if="bulkMessage" class="mb-4 p-3 rounded border text-sm font-medium"
            :class="bulkMessage.includes('lỗi') || bulkMessage.includes('không') ? 'bg-red-50 border-red-200 text-red-700' : 'bg-green-50 border-green-200 text-green-700'">
            {{ bulkMessage }}
            <button @click="bulkMessage = ''" class="ml-2 underline text-xs">Đóng</button>
        </div>

        <div class="bg-white rounded border border-[#E8D9C5] overflow-hidden shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-[#FAF6F0]">
                    <tr>
                        <th class="px-4 py-3 text-center w-10">
                            <input
                                type="checkbox"
                                :checked="isAllSelected"
                                @change="toggleAll"
                                class="rounded border-[#E8D9C5] text-[#D4A853] focus:ring-[#D4A853]"
                                title="Chọn tất cả đơn chờ xác nhận"
                            />
                        </th>
                        <th class="px-4 py-3 text-left text-gray-500">Mã đơn</th>
                        <th class="px-4 py-3 text-left text-gray-500">Khách hàng</th>
                        <th class="px-4 py-3 text-center text-gray-500">Trạng thái</th>
                        <th class="px-4 py-3 text-right text-gray-500">Tổng tiền</th>
                        <th class="px-4 py-3 text-right text-gray-500">Ngày tạo</th>
                        <th class="px-4 py-3 text-center text-gray-500">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="order in orders.data" :key="order.id"
                        class="hover:bg-[#FAF6F0] transition"
                        :class="selectedIds.includes(order.id) ? 'bg-amber-50' : ''">
                        <td class="px-4 py-4 text-center">
                            <input
                                v-if="order.status === 'pending'"
                                type="checkbox"
                                :value="order.id"
                                v-model="selectedIds"
                                class="rounded border-[#E8D9C5] text-[#D4A853] focus:ring-[#D4A853]"
                            />
                        </td>
                        <td class="px-4 py-4 font-medium">#{{ order.order_number }}</td>
                        <td class="px-4 py-4">{{ order.customer_name }}</td>
                        <td class="px-4 py-4 text-center">
                            <span :class="statusColors[order.status_color]" class="px-2 py-1 rounded-full text-xs font-semibold">{{ order.status_label }}</span>
                        </td>
                        <td class="px-4 py-4 text-right font-medium">{{ formatCurrency(order.total) }}</td>
                        <td class="px-4 py-4 text-right text-gray-500">{{ order.created_at }}</td>
                        <td class="px-4 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <!-- Quick Confirm Button -->
                                <button
                                    v-if="order.status === 'pending'"
                                    @click="quickConfirm(order)"
                                    :disabled="quickLoading[order.id]"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-semibold transition disabled:opacity-50"
                                    title="Xác nhận đơn hàng"
                                >
                                    <svg v-if="quickLoading[order.id]" class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                                    <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Xác nhận
                                </button>
                                <!-- View Detail Link -->
                                <Link :href="`/admin/orders/${order.id}`" class="inline-flex items-center gap-1 px-3 py-1.5 border border-[#E8D9C5] hover:border-[#D4A853] text-[#2C1810] rounded text-xs font-medium transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Chi tiết
                                </Link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <Pagination :links="orders.links" />
    </AdminLayout>
</template>
