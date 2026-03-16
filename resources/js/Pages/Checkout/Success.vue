<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    order: Object,
});
</script>

<template>
    <AppLayout>
        <div class="max-w-lg mx-auto px-4 py-16 text-center">
            <div class="bg-white rounded-2xl shadow-lg p-10">
                <p class="text-7xl mb-6">🎉</p>
                <h1 class="text-3xl font-bold text-amber-900 mb-4">Đặt hàng thành công!</h1>
                <p class="text-gray-600 mb-2">Cảm ơn bạn đã đặt hàng.</p>
                <p v-if="order" class="text-lg font-semibold text-amber-700 mb-8">Mã đơn: #{{ order.order_number }}</p>

                <div class="space-y-3">
                    <Link v-if="order" :href="`/orders/${order.id}`" class="block w-full bg-amber-700 text-white py-3 rounded-xl font-semibold hover:bg-amber-600 transition">
                        Xem đơn hàng
                    </Link>
                    <Link href="/menu" class="block w-full border-2 border-amber-700 text-amber-700 py-3 rounded-xl font-semibold hover:bg-amber-50 transition">
                        Tiếp tục mua hàng
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
