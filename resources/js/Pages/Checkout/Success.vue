<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    order: Object,
});
</script>

<template>
    <AppLayout>
        <div class="max-w-lg mx-auto px-4 py-20 text-center">
            <div class="bg-white p-12 shadow-sm">
                <div class="w-16 h-16 mx-auto mb-6 bg-green-50 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h1 class="text-3xl font-bold text-[#1a1a1a] mb-3" style="font-family: 'Playfair Display', serif;">Đặt hàng thành công!</h1>
                <p class="text-gray-500 mb-2">Cảm ơn bạn đã đặt hàng.</p>
                <p v-if="order" class="text-sm font-semibold text-[#1a1a1a] mb-10">Mã đơn: #{{ order.order_number }}</p>

                <div class="space-y-3">
                    <Link v-if="order" :href="`/orders/${order.id}`" class="block w-full bg-[#1a1a1a] text-white py-3.5 text-sm font-semibold tracking-wider uppercase hover:bg-[#333] transition">
                        Xem đơn hàng
                    </Link>
                    <Link href="/menu" class="block w-full border border-[#1a1a1a] text-[#1a1a1a] py-3.5 text-sm font-semibold tracking-wider uppercase hover:bg-[#1a1a1a] hover:text-white transition">
                        Tiếp tục mua hàng
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
