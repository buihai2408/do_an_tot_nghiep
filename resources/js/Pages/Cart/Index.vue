<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useFormatters } from '@/Composables/useFormatters';
import { useCart } from '@/Composables/useCart';

const { formatCurrency } = useFormatters();
const { updateItem, removeItem, clearCart, loading } = useCart();

defineProps({
    cart: Object,
    summary: Object,
});
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold text-amber-900 mb-8">Giỏ hàng</h1>

            <div v-if="cart.items.length === 0" class="bg-white rounded-2xl shadow-md p-12 text-center">
                <p class="text-6xl mb-4">🛒</p>
                <p class="text-xl text-gray-500 mb-6">Giỏ hàng trống</p>
                <Link href="/menu" class="inline-block bg-amber-700 text-white px-8 py-3 rounded-full font-semibold hover:bg-amber-600 transition">
                    Khám phá thực đơn →
                </Link>
            </div>

            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Items -->
                <div class="lg:col-span-2 space-y-4">
                    <div v-for="item in cart.items" :key="item.id" class="bg-white rounded-2xl shadow-md p-6 flex items-start space-x-4">
                        <div class="w-20 h-20 bg-amber-100 rounded-xl flex items-center justify-center text-3xl flex-shrink-0">☕</div>
                        <div class="flex-1 min-w-0">
                            <Link :href="`/menu/${item.product.slug}`" class="font-semibold text-amber-900 hover:text-amber-700">
                                {{ item.product.name }}
                            </Link>
                            <div class="text-sm text-gray-500 mt-1">
                                <span v-if="item.size">Size {{ item.size.name }}</span>
                                <span v-if="item.toppings.length" class="ml-2">
                                    + {{ item.toppings.map(t => t.name).join(', ') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between mt-3">
                                <div class="flex items-center border rounded-lg">
                                    <button @click="updateItem(item.id, Math.max(1, item.quantity - 1))" class="px-3 py-1 text-amber-700 hover:bg-amber-50">-</button>
                                    <span class="px-3 py-1 font-semibold">{{ item.quantity }}</span>
                                    <button @click="updateItem(item.id, item.quantity + 1)" class="px-3 py-1 text-amber-700 hover:bg-amber-50">+</button>
                                </div>
                                <span class="font-bold text-amber-700">{{ formatCurrency(item.total) }}</span>
                            </div>
                        </div>
                        <button @click="removeItem(item.id)" class="text-red-400 hover:text-red-600 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                    <button @click="clearCart" class="text-sm text-red-500 hover:text-red-700 transition">Xóa toàn bộ giỏ hàng</button>
                </div>

                <!-- Summary -->
                <div class="bg-white rounded-2xl shadow-md p-6 h-fit sticky top-24">
                    <h2 class="text-xl font-bold text-amber-900 mb-4">Tóm tắt</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tạm tính</span>
                            <span class="font-medium">{{ formatCurrency(summary.subtotal) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phí giao hàng</span>
                            <span class="font-medium" :class="summary.shipping_fee === 0 ? 'text-green-600' : ''">
                                {{ summary.shipping_fee === 0 ? 'Miễn phí' : formatCurrency(summary.shipping_fee) }}
                            </span>
                        </div>
                        <hr />
                        <div class="flex justify-between text-lg font-bold text-amber-900">
                            <span>Tổng cộng</span>
                            <span>{{ formatCurrency(summary.total) }}</span>
                        </div>
                    </div>
                    <Link href="/checkout" class="block w-full bg-amber-700 text-white py-3 rounded-xl font-semibold text-center hover:bg-amber-600 transition mt-6">
                        Thanh toán
                    </Link>
                    <p v-if="summary.shipping_fee > 0" class="text-xs text-gray-500 mt-3 text-center">
                        Miễn phí giao hàng cho đơn từ {{ formatCurrency(100000) }}
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
