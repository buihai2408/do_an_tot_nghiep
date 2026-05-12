<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useFormatters } from '@/Composables/useFormatters';
import axios from 'axios';

const { formatCurrency } = useFormatters();
const props = defineProps({ order: Object, allowedTransitions: Array });

const selectedStatus = ref('');
const cancelReason = ref('');

const updateStatus = async () => {
    if (!selectedStatus.value) return;
    try {
        await axios.put(`/api/admin/orders/${props.order.id}/status`, {
            status: selectedStatus.value,
            cancel_reason: selectedStatus.value === 'cancelled' ? cancelReason.value : null,
        });
        router.reload();
    } catch (e) {
        alert(e.response?.data?.message || 'Không thể cập nhật');
    }
};
</script>

<template>
    <AdminLayout>
        <div class="max-w-4xl">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-[#1a1a1a] font-serif">#{{ order.order_number }}</h1>
                <span class="px-4 py-2 rounded-full text-sm font-semibold bg-amber-100 text-amber-800">{{ order.status_label }}</span>
            </div>

            <!-- Status transition -->
            <div v-if="allowedTransitions.length" class="bg-white rounded border border-[#E8D9C5] p-6 mb-6 shadow-sm">
                <h2 class="font-bold mb-3">Cập nhật trạng thái</h2>
                <div class="flex items-center space-x-4">
                    <select v-model="selectedStatus" class="rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853] flex-1">
                        <option value="">Chọn trạng thái</option>
                        <option v-for="t in allowedTransitions" :key="t.value" :value="t.value">{{ t.label }}</option>
                    </select>
                    <button @click="updateStatus" :disabled="!selectedStatus" class="px-6 py-2 bg-[#2C1810] text-white rounded disabled:opacity-50 transition">Cập nhật</button>
                </div>
                <textarea v-if="selectedStatus === 'cancelled'" v-model="cancelReason" rows="2" placeholder="Lý do hủy..." class="w-full rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853] mt-3"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded border border-[#E8D9C5] p-6 shadow-sm">
                    <h2 class="font-bold mb-3">Thông tin khách hàng</h2>
                    <p>{{ order.customer_name }} - {{ order.customer_phone }}</p>
                    <p v-if="order.customer_email" class="text-gray-500">{{ order.customer_email }}</p>
                    <p v-if="order.shipping_address" class="text-gray-500 mt-1">{{ order.shipping_address }}</p>
                    <p class="text-[#D4A853] mt-1">{{ order.order_type }}</p>
                </div>
                <div class="bg-white rounded border border-[#E8D9C5] p-6 shadow-sm">
                    <h2 class="font-bold mb-3">Thanh toán</h2>
                    <p>{{ order.payment_method }}</p>
                    <p class="text-gray-500">{{ order.payment_status }}</p>
                    <p v-if="order.coupon" class="text-green-600 mt-1">Mã giảm giá: {{ order.coupon.code }}</p>
                </div>
            </div>

            <!-- Items -->
            <div class="bg-white rounded border border-[#E8D9C5] p-6 shadow-sm">
                <h2 class="font-bold mb-4">Sản phẩm</h2>
                <div class="space-y-3">
                    <div v-for="item in order.items" :key="item.product_name" class="flex justify-between p-3 bg-[#FAF6F0] rounded">
                        <div>
                            <p class="font-medium">{{ item.product_name }} x{{ item.quantity }}</p>
                            <p class="text-sm text-gray-500">
                                <span v-if="item.size_name">Size {{ item.size_name }}</span>
                                <span v-if="item.toppings?.length"> · {{ item.toppings.map(t => t.name).join(', ') }}</span>
                            </p>
                        </div>
                        <span class="font-medium">{{ formatCurrency(item.subtotal) }}</span>
                    </div>
                </div>
                <hr class="my-4" />
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span>Tạm tính</span><span>{{ formatCurrency(order.subtotal) }}</span></div>
                    <div v-if="order.discount_amount > 0" class="flex justify-between text-green-600"><span>Giảm giá</span><span>-{{ formatCurrency(order.discount_amount) }}</span></div>
                    <div class="flex justify-between"><span>Phí giao</span><span>{{ formatCurrency(order.shipping_fee) }}</span></div>
                    <hr />
                    <div class="flex justify-between text-lg font-bold"><span>Tổng</span><span class="text-[#D4A853]">{{ formatCurrency(order.total) }}</span></div>
                </div>
                <p v-if="order.note" class="mt-4 text-sm bg-amber-50 p-3 rounded-lg">📝 {{ order.note }}</p>
            </div>
        </div>
    </AdminLayout>
</template>
