<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useFormatters } from '@/Composables/useFormatters';

const { formatCurrency } = useFormatters();

defineProps({ orders: Object });

const statusColors = {
    yellow: 'bg-yellow-100 text-yellow-800',
    blue: 'bg-blue-100 text-blue-800',
    indigo: 'bg-indigo-100 text-indigo-800',
    purple: 'bg-purple-100 text-purple-800',
    orange: 'bg-orange-100 text-orange-800',
    green: 'bg-green-100 text-green-800',
    red: 'bg-red-100 text-red-800',
};
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold text-amber-900 mb-8">Đơn hàng của tôi</h1>

            <div v-if="orders.data.length === 0" class="bg-white rounded-2xl shadow-md p-12 text-center">
                <p class="text-6xl mb-4">📋</p>
                <p class="text-xl text-gray-500 mb-6">Bạn chưa có đơn hàng nào</p>
                <Link href="/menu" class="inline-block bg-amber-700 text-white px-8 py-3 rounded-full font-semibold hover:bg-amber-600 transition">Đặt hàng ngay →</Link>
            </div>

            <div v-else class="space-y-4">
                <Link
                    v-for="order in orders.data"
                    :key="order.id"
                    :href="`/orders/${order.id}`"
                    class="block bg-white rounded-2xl shadow-md p-6 hover:shadow-lg transition"
                >
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-bold text-amber-900">#{{ order.order_number }}</span>
                        <span :class="statusColors[order.status_color]" class="px-3 py-1 rounded-full text-xs font-semibold">
                            {{ order.status_label }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm text-gray-500">
                        <span>{{ order.items_count }} sản phẩm · {{ order.created_at }}</span>
                        <span class="font-bold text-amber-700 text-lg">{{ formatCurrency(order.total) }}</span>
                    </div>
                </Link>
            </div>

            <!-- Pagination -->
            <div v-if="orders.links?.length > 3" class="flex justify-center mt-8 space-x-1">
                <template v-for="link in orders.links" :key="link.label">
                    <Link v-if="link.url" :href="link.url" v-html="link.label" class="px-4 py-2 rounded-lg text-sm" :class="link.active ? 'bg-amber-700 text-white' : 'bg-white text-amber-700 hover:bg-amber-100'" />
                    <span v-else v-html="link.label" class="px-4 py-2 text-sm text-gray-400" />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
