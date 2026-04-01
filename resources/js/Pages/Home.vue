<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useFormatters } from '@/Composables/useFormatters';

const { formatCurrency } = useFormatters();

defineProps({
    featuredProducts: Array,
    categories: Array,
});
</script>

<template>
    <AppLayout>
        <!-- Hero -->
        <section class="relative flex items-center justify-center min-h-[80vh] bg-cover bg-center bg-no-repeat" style="background-image: url('/images/background_home.jpg')">
            <div class="absolute inset-0 bg-black/50"></div>
            <div class="relative z-10 text-center px-4 max-w-4xl mx-auto py-20">
                <div class="mb-6">
                    <svg class="w-16 h-16 mx-auto text-white/80" fill="currentColor" viewBox="0 0 24 24"><path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/></svg>
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-7xl font-bold text-white mb-6 leading-tight tracking-wide" style="font-family: 'Playfair Display', serif;">
                    GREAT COFFEE,<br>EVERY DAY
                </h1>
                <p class="text-base sm:text-lg text-gray-300 mb-10 max-w-2xl mx-auto leading-relaxed">
                    Thưởng thức hương vị cà phê đích thực, pha chế từ những hạt cà phê được chọn lọc kỹ lưỡng, mang đến trải nghiệm tuyệt vời mỗi ngày.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <Link href="/menu" class="px-8 py-3.5 bg-white text-[#1a1a1a] text-sm font-semibold tracking-widest uppercase hover:bg-gray-100 transition">
                        Xem Menu
                    </Link>
                    <Link href="/menu" class="px-8 py-3.5 border-2 border-white text-white text-sm font-semibold tracking-widest uppercase hover:bg-white hover:text-[#1a1a1a] transition">
                        Khám Phá
                    </Link>
                </div>
            </div>
        </section>

        <!-- Feature Section - "We Serve The Best Coffee" -->
        <section class="py-20 lg:py-28 bg-[#f8f5f0]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-xl mb-16">
                    <h2 class="text-3xl lg:text-4xl font-bold text-[#1a1a1a] leading-tight" style="font-family: 'Playfair Display', serif;">
                        Chúng Tôi Phục Vụ<br>Cà Phê Ngon Nhất.
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div v-for="(cat, index) in categories?.slice(0, 3)" :key="cat.id" class="group">
                        <Link :href="`/menu?category=${cat.slug}`">
                            <div class="aspect-[4/3] bg-gray-200 mb-6 overflow-hidden">
                                <img v-if="cat.featured_image" :src="`/storage/${cat.featured_image}`" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                <div v-else class="w-full h-full bg-gradient-to-br flex items-center justify-center"
                                     :class="index === 0 ? 'from-amber-100 to-amber-200' : index === 1 ? 'from-orange-100 to-orange-200' : 'from-yellow-100 to-yellow-200'">
                                    <svg class="w-16 h-16 text-amber-600/40" fill="currentColor" viewBox="0 0 24 24"><path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/></svg>
                                </div>
                            </div>
                            <h3 class="text-sm font-bold tracking-widest uppercase text-[#1a1a1a] mb-3">{{ cat.name }}</h3>
                            <p class="text-sm text-gray-500 leading-relaxed mb-4">Khám phá các sản phẩm {{ cat.name.toLowerCase() }} được pha chế tỉ mỉ từ nguyên liệu chất lượng cao.</p>
                            <span class="text-sm font-medium text-[#1a1a1a] border-b border-[#1a1a1a] pb-0.5 group-hover:border-amber-600 group-hover:text-amber-700 transition">Xem thêm</span>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Parallax Banner -->
        <section class="relative h-[60vh] bg-fixed bg-cover bg-center" style="background-image: url('/images/background_home.jpg')">
            <div class="absolute inset-0 bg-black/40"></div>
            <div class="relative z-10 h-full flex items-center justify-end">
                <div class="bg-[#1a1a1a]/80 backdrop-blur-sm p-12 lg:p-16 mr-8 lg:mr-24 max-w-md">
                    <h2 class="text-3xl lg:text-4xl font-bold text-white leading-tight" style="font-family: 'Playfair Display', serif;">
                        THE BEST<br>COFFEE SHOP
                    </h2>
                </div>
            </div>
        </section>

        <!-- Opening Hours Section -->
        <section class="relative py-20 lg:py-28 bg-[#1a1a1a]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <p class="text-xs font-semibold tracking-widest uppercase text-amber-500 mb-4">Thời gian hoạt động</p>
                        <h2 class="text-3xl lg:text-5xl font-bold text-white leading-tight mb-6" style="font-family: 'Playfair Display', serif;">
                            Chúng Tôi Mở Cửa<br>Hàng Ngày Từ 07:00.<br>Hẹn Gặp Bạn!
                        </h2>
                        <p class="text-gray-400 leading-relaxed max-w-lg">
                            Ghé thăm chúng tôi để thưởng thức những ly cà phê thơm ngon nhất. Không gian ấm cúng, phục vụ tận tâm, mang đến cho bạn những giây phút thư giãn tuyệt vời.
                        </p>
                    </div>
                    <div class="aspect-[4/3] overflow-hidden">
                        <img src="/images/background_home.jpg" class="w-full h-full object-cover" alt="Coffee shop interior" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="py-20 lg:py-28 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <p class="text-xs font-semibold tracking-widest uppercase text-amber-600 mb-3">Sản phẩm nổi bật</p>
                    <h2 class="text-3xl lg:text-4xl font-bold text-[#1a1a1a]" style="font-family: 'Playfair Display', serif;">
                        Menu Đặc Biệt
                    </h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 lg:gap-8">
                    <Link
                        v-for="product in featuredProducts"
                        :key="product.id"
                        :href="`/menu/${product.slug}`"
                        class="group"
                    >
                        <div class="aspect-square bg-[#f8f5f0] overflow-hidden mb-4">
                            <img v-if="product.image" :src="`/storage/${product.image}`" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <svg class="w-16 h-16 text-amber-300" fill="currentColor" viewBox="0 0 24 24"><path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/></svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">{{ product.category }}</p>
                        <h3 class="text-sm font-semibold text-[#1a1a1a] mb-1 group-hover:text-amber-700 transition">{{ product.name }}</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-[#1a1a1a]">{{ formatCurrency(product.base_price) }}</span>
                            <span v-if="product.avg_rating > 0" class="text-xs text-amber-600">★ {{ product.avg_rating }}</span>
                        </div>
                    </Link>
                </div>
                <div class="text-center mt-14">
                    <Link href="/menu" class="inline-block px-10 py-3.5 bg-[#1a1a1a] text-white text-sm font-semibold tracking-widest uppercase hover:bg-[#333] transition">
                        Xem Tất Cả Menu
                    </Link>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="relative py-20 lg:py-24 bg-cover bg-center" style="background-image: url('/images/background_home.jpg')">
            <div class="absolute inset-0 bg-[#1a1a1a]/85"></div>
            <div class="relative z-10 text-center px-4 max-w-3xl mx-auto">
                <p class="text-xs font-semibold tracking-widest uppercase text-amber-500 mb-4">Specialty Coffee & More</p>
                <h2 class="text-3xl lg:text-5xl font-bold text-white mb-6" style="font-family: 'Playfair Display', serif;">
                    VISIT OUR COFFEE SHOP
                </h2>
                <p class="text-gray-400 mb-10 leading-relaxed">
                    Số 99, Đường Cà Phê, Quận 1, TP.HCM<br>
                    Mở cửa hàng ngày từ 07:00 đến 22:00
                </p>
                <Link href="/menu" class="inline-block px-10 py-3.5 border-2 border-white text-white text-sm font-semibold tracking-widest uppercase hover:bg-white hover:text-[#1a1a1a] transition">
                    Đặt Hàng Ngay
                </Link>
            </div>
        </section>
    </AppLayout>
</template>
