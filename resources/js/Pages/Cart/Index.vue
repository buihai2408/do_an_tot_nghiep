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
        
        <section class="py-16 lg:py-20" style="background:#2C1810;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-xs font-semibold tracking-[0.3em] uppercase mb-3 flex items-center justify-center gap-3" style="color:#D4A853;">
                    <span class="w-6 h-px" style="background:#D4A853;"></span>
                    Thanh toán
                    <span class="w-6 h-px" style="background:#D4A853;"></span>
                </p>
                <h1 class="text-4xl lg:text-5xl font-bold text-white" style="font-family:'Playfair Display',serif;">Giỏ hàng của bạn</h1>
            </div>
        </section>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20">
            <div v-if="cart.items.length === 0" class="text-center py-24 bg-white rounded-2xl border" style="border-color:#E8D9C5; box-shadow:0 10px 30px rgba(44,24,16,0.03);">
                <div class="w-24 h-24 mx-auto mb-6 flex items-center justify-center rounded-full" style="background:#FAF6F0;">
                    <svg class="w-12 h-12" style="color:#D4A853;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                    </svg>
                </div>
                <p class="text-xl font-bold mb-8" style="color:#2C1810;">Giỏ hàng của bạn đang trống</p>
                <Link href="/menu" class="inline-block px-10 py-4 text-sm font-bold tracking-widest uppercase rounded-xl transition-all duration-300" style="background:#2C1810; color:white; box-shadow:0 10px 20px rgba(44,24,16,0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 15px 25px rgba(44,24,16,0.3)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 10px 20px rgba(44,24,16,0.2)';">
                    Khám Phá Menu Ngay
                </Link>
            </div>

            <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                
                <div class="lg:col-span-8">
                    <div class="bg-white rounded-2xl border overflow-hidden" style="border-color:#E8D9C5; box-shadow:0 10px 30px rgba(44,24,16,0.03);">
                        
                        <div class="hidden md:grid grid-cols-12 gap-4 px-8 py-5 text-xs font-bold tracking-widest uppercase" style="background:#FAF6F0; color:#A16A38; border-bottom:1px solid #E8D9C5;">
                            <div class="col-span-6">Sản phẩm</div>
                            <div class="col-span-3 text-center">Số lượng</div>
                            <div class="col-span-2 text-right">Thành tiền</div>
                            <div class="col-span-1"></div>
                        </div>

                        
                        <div class="divide-y" style="border-color:#F2EBE0;">
                            <div v-for="item in cart.items" :key="item.id" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center p-6 lg:p-8 transition-colors hover:bg-[#FAF6F0]/50">
                                <div class="md:col-span-6 flex items-start gap-5">
                                    <div class="w-24 h-24 rounded-lg flex items-center justify-center flex-shrink-0 border" style="background:#F8F5F0; border-color:#E8D9C5;">
                                        <img v-if="item.product.image" :src="`/storage/${item.product.image}`" class="w-full h-full object-cover rounded-lg" />
                                        <svg v-else class="w-10 h-10 opacity-30" style="color:#2C1810;" fill="currentColor" viewBox="0 0 24 24"><path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/></svg>
                                    </div>
                                    <div>
                                        <Link :href="`/menu/${item.product.slug}`" class="font-bold text-base transition-colors" style="color:#2C1810;" onmouseover="this.style.color='#D4A853'" onmouseout="this.style.color='#2C1810'">
                                            {{ item.product.name }}
                                        </Link>
                                        <div class="text-xs mt-2 space-y-1.5" style="color:#8B7355;">
                                            <p v-if="item.size"><span class="font-semibold" style="color:#6B5340;">Size:</span> {{ item.size.name }}</p>
                                            <p v-if="item.ice_level"><span class="font-semibold" style="color:#6B5340;">Đá:</span> {{ item.ice_level }}</p>
                                            <p v-if="item.sugar_level"><span class="font-semibold" style="color:#6B5340;">Đường:</span> {{ item.sugar_level }}</p>
                                            <p v-if="item.toppings.length"><span class="font-semibold" style="color:#6B5340;">Topping:</span> {{ item.toppings.map(t => t.name).join(', ') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="md:col-span-3 flex justify-start md:justify-center">
                                    <div class="flex items-center bg-white rounded-lg border overflow-hidden" style="border-color:#E8D9C5;">
                                        <button @click="updateItem(item.id, Math.max(1, item.quantity - 1))" class="px-4 py-2.5 text-lg transition-colors hover:bg-[#FAF6F0]" style="color:#2C1810;">−</button>
                                        <span class="px-2 py-2.5 text-sm font-bold min-w-[2.5rem] text-center" style="color:#2C1810;">{{ item.quantity }}</span>
                                        <button @click="updateItem(item.id, item.quantity + 1)" class="px-4 py-2.5 text-lg transition-colors hover:bg-[#FAF6F0]" style="color:#2C1810;">+</button>
                                    </div>
                                </div>
                                <div class="md:col-span-2 text-left md:text-right">
                                    <span class="font-bold text-base" style="color:#2C1810;">{{ formatCurrency(item.total) }}</span>
                                </div>
                                <div class="md:col-span-1 text-right">
                                    <button @click="removeItem(item.id)" class="w-8 h-8 rounded-full flex items-center justify-center transition-colors ml-auto" style="color:#A16A38; background:#FAF6F0;" onmouseover="this.style.background='#FEE2E2'; this.style.color='#DC2626'" onmouseout="this.style.background='#FAF6F0'; this.style.color='#A16A38'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 text-right">
                        <button @click="clearCart" class="text-xs font-bold tracking-widest uppercase transition-colors" style="color:#A16A38;" onmouseover="this.style.color='#DC2626'" onmouseout="this.style.color='#A16A38'">
                            <span class="border-b border-transparent hover:border-red-500 pb-1">Xóa toàn bộ giỏ hàng</span>
                        </button>
                    </div>
                </div>

                
                <div class="lg:col-span-4">
                    <div class="bg-white p-8 rounded-2xl border sticky top-28" style="border-color:#E8D9C5; box-shadow:0 20px 40px rgba(44,24,16,0.06);">
                        <h2 class="text-lg font-bold mb-6 flex items-center gap-2" style="color:#2C1810;">
                            <span>🧾</span> Tóm tắt đơn hàng
                        </h2>
                        
                        <div class="space-y-4 text-sm mb-6">
                            <div class="flex justify-between items-center">
                                <span style="color:#6B5340;">Tạm tính ({{ cart.items.length }} sản phẩm)</span>
                                <span class="font-bold" style="color:#2C1810;">{{ formatCurrency(summary.subtotal) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span style="color:#6B5340;">Phí giao hàng</span>
                                <span class="font-bold" :style="summary.shipping_fee === 0 ? 'color:#16A34A;' : 'color:#2C1810;'">
                                    {{ summary.shipping_fee === 0 ? 'Miễn phí' : formatCurrency(summary.shipping_fee) }}
                                </span>
                            </div>
                        </div>

                        <div class="pt-5 border-t border-dashed mb-8" style="border-color:#D4A853;">
                            <div class="flex justify-between items-end">
                                <span class="text-base font-bold" style="color:#2C1810;">Tổng cộng</span>
                                <span class="text-2xl font-bold" style="color:#2C1810;">{{ formatCurrency(summary.total) }}</span>
                            </div>
                            <p class="text-[10px] mt-1 text-right italic" style="color:#8B7355;">(Đã bao gồm VAT nếu có)</p>
                        </div>

                        <Link href="/checkout" class="w-full flex items-center justify-center py-4 text-sm font-bold tracking-widest uppercase transition-all duration-300 rounded-xl group" style="background:#2C1810; color:white; box-shadow:0 10px 20px rgba(44,24,16,0.15);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 15px 25px rgba(44,24,16,0.25)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 10px 20px rgba(44,24,16,0.15)';">
                            Tiến hành thanh toán
                            <svg class="w-4 h-4 ml-2 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7-7m7 7H3"/></svg>
                        </Link>

                        <div class="mt-6 p-4 rounded-lg bg-[#FAF6F0] border border-[#E8D9C5] flex items-start gap-3">
                            <span class="text-lg">🚚</span>
                            <p class="text-xs leading-relaxed" style="color:#6B5340;">
                                Miễn phí giao hàng cho đơn hàng từ <strong style="color:#2C1810;">{{ formatCurrency(100000) }}</strong>. Thời gian giao hàng dự kiến từ 15-30 phút.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
