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
        <!-- Page Header -->
        <section class="bg-[#1a1a1a] py-16 lg:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl lg:text-5xl font-bold text-white" style="font-family: 'Playfair Display', serif;">Giỏ hàng</h1>
            </div>
        </section>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div v-if="cart.items.length === 0" class="text-center py-20">
                <svg class="w-20 h-20 mx-auto mb-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                </svg>
                <p class="text-xl text-gray-400 mb-8">Giỏ hàng trống</p>
                <Link href="/menu" class="inline-block px-10 py-3.5 bg-[#1a1a1a] text-white text-sm font-semibold tracking-widest uppercase hover:bg-[#333] transition">
                    Khám Phá Menu
                </Link>
            </div>

            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <!-- Items -->
                <div class="lg:col-span-2">
                    <div class="border-b border-gray-200 pb-3 mb-6 hidden md:grid grid-cols-12 text-xs font-semibold tracking-widest uppercase text-gray-400">
                        <div class="col-span-6">Sản phẩm</div>
                        <div class="col-span-2 text-center">Số lượng</div>
                        <div class="col-span-3 text-right">Thành tiền</div>
                        <div class="col-span-1"></div>
                    </div>

                    <div v-for="item in cart.items" :key="item.id" class="border-b border-gray-100 py-6 grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                        <div class="md:col-span-6 flex items-start gap-4">
                            <div class="w-20 h-20 bg-[#f8f5f0] flex items-center justify-center flex-shrink-0">
                                <svg class="w-8 h-8 text-amber-300" fill="currentColor" viewBox="0 0 24 24"><path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/></svg>
                            </div>
                            <div>
                                <Link :href="`/menu/${item.product.slug}`" class="font-semibold text-sm text-[#1a1a1a] hover:text-amber-700 transition">
                                    {{ item.product.name }}
                                </Link>
                                <div class="text-xs text-gray-400 mt-1 space-y-0.5">
                                    <p v-if="item.size">Size: {{ item.size.name }}</p>
                                    <p v-if="item.toppings.length">Topping: {{ item.toppings.map(t => t.name).join(', ') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="md:col-span-2 flex justify-center">
                            <div class="flex items-center border border-gray-300">
                                <button @click="updateItem(item.id, Math.max(1, item.quantity - 1))" class="px-3 py-1.5 text-sm hover:bg-gray-100 transition">−</button>
                                <span class="px-3 py-1.5 text-sm font-semibold min-w-[2rem] text-center">{{ item.quantity }}</span>
                                <button @click="updateItem(item.id, item.quantity + 1)" class="px-3 py-1.5 text-sm hover:bg-gray-100 transition">+</button>
                            </div>
                        </div>
                        <div class="md:col-span-3 text-right">
                            <span class="font-bold text-sm text-[#1a1a1a]">{{ formatCurrency(item.total) }}</span>
                        </div>
                        <div class="md:col-span-1 text-right">
                            <button @click="removeItem(item.id)" class="text-gray-400 hover:text-red-500 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button @click="clearCart" class="text-xs text-gray-400 hover:text-red-500 tracking-widest uppercase transition">Xóa toàn bộ</button>
                    </div>
                </div>

                <!-- Summary -->
                <div>
                    <div class="bg-[#f8f5f0] p-8 sticky top-24">
                        <h2 class="text-xs font-semibold tracking-widest uppercase text-[#1a1a1a] mb-6">Tóm tắt đơn hàng</h2>
                        <div class="space-y-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tạm tính</span>
                                <span class="font-medium text-[#1a1a1a]">{{ formatCurrency(summary.subtotal) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Phí giao hàng</span>
                                <span class="font-medium" :class="summary.shipping_fee === 0 ? 'text-green-600' : 'text-[#1a1a1a]'">
                                    {{ summary.shipping_fee === 0 ? 'Miễn phí' : formatCurrency(summary.shipping_fee) }}
                                </span>
                            </div>
                            <hr class="border-gray-300">
                            <div class="flex justify-between text-base font-bold text-[#1a1a1a]">
                                <span>Tổng cộng</span>
                                <span>{{ formatCurrency(summary.total) }}</span>
                            </div>
                        </div>
                        <Link href="/checkout" class="block w-full bg-[#1a1a1a] text-white py-3.5 text-sm font-semibold tracking-wider uppercase text-center hover:bg-[#333] transition mt-8">
                            Thanh toán
                        </Link>
                        <p v-if="summary.shipping_fee > 0" class="text-[10px] text-gray-400 mt-4 text-center">
                            Miễn phí giao hàng cho đơn từ {{ formatCurrency(100000) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
