<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { useFormatters } from '@/Composables/useFormatters';
import { onMounted, onUnmounted } from 'vue';

const { formatCurrency } = useFormatters();

const props = defineProps({ orders: Object });

const statusColors = {
    yellow: 'bg-yellow-100 text-yellow-800',
    blue: 'bg-blue-100 text-blue-800',
    indigo: 'bg-indigo-100 text-indigo-800',
    purple: 'bg-purple-100 text-purple-800',
    orange: 'bg-orange-100 text-orange-800',
    green: 'bg-green-100 text-green-800',
    red: 'bg-red-100 text-red-800',
};

onMounted(() => {
    if (window.Echo && props.orders?.data) {
        props.orders.data.forEach(order => {
            window.Echo.private(`orders.${order.id}`)
                .listen('.OrderStatusUpdated', () => {
                    router.reload({ only: ['orders'] });
                });
        });
    }
});

onUnmounted(() => {
    if (window.Echo && props.orders?.data) {
        props.orders.data.forEach(order => {
            window.Echo.leave(`orders.${order.id}`);
        });
    }
});
</script>

<template>
    <AppLayout>
        
        <section class="bg-[#1a1a1a] py-16 lg:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl lg:text-5xl font-bold text-white" style="font-family: 'Playfair Display', serif;">Đơn hàng của tôi</h1>
            </div>
        </section>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div v-if="orders.data.length === 0" class="text-center py-20">
                <svg class="w-20 h-20 mx-auto mb-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-xl text-gray-400 mb-8">Bạn chưa có đơn hàng nào</p>
                <Link href="/menu" class="inline-block px-10 py-3.5 bg-[#1a1a1a] text-white text-sm font-semibold tracking-widest uppercase hover:bg-[#333] transition">Đặt hàng ngay</Link>
            </div>

            <div v-else class="space-y-4">
                <Link
                    v-for="order in orders.data"
                    :key="order.id"
                    :href="`/orders/${order.id}`"
                    class="block bg-white border border-gray-100 p-6 hover:border-gray-300 transition"
                >
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-bold text-[#1a1a1a]">#{{ order.order_number }}</span>
                        <span :class="statusColors[order.status_color]" class="px-3 py-1 text-xs font-semibold">
                            {{ order.status_label }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm text-gray-500">
                        <span>{{ order.items_count }} sản phẩm · {{ order.created_at }}</span>
                        <span class="font-bold text-[#1a1a1a] text-lg">{{ formatCurrency(order.total) }}</span>
                    </div>
                </Link>
            </div>

            
            <div v-if="orders.links?.length > 3" class="flex justify-center mt-12 space-x-1">
                <template v-for="link in orders.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                        class="px-4 py-2 text-sm border transition"
                        :class="link.active ? 'bg-[#1a1a1a] text-white border-[#1a1a1a]' : 'bg-white text-gray-600 border-gray-300 hover:border-[#1a1a1a]'"
                    />
                    <span v-else v-html="link.label" class="px-4 py-2 text-sm text-gray-300" />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
