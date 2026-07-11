<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useFormatters } from '@/Composables/useFormatters';

const { formatCurrency } = useFormatters();

defineProps({
    featuredProducts: Array,
    categories: Array,
});


const categoryImage = (slug) => {
    const map = {
        'ca-phe':       '/images/categories/ca-phe.png',
        'tra':          '/images/categories/tra.png',
        'banh-ngot':    '/images/categories/banh-ngot.png',
        'frappe':       '/images/categories/frappe.png',
        'freeze-da-xay':'/images/categories/freeze-da-xay.png',
        'sinh-to':      '/images/categories/sinh-to.png',
    };
    return map[slug] || '/images/categories/ca-phe.png';
};
</script>

<template>
    <AppLayout>
        
        <section class="relative flex items-center justify-center min-h-[90vh] bg-cover bg-center bg-no-repeat" style="background-image:url('/images/background_home.jpg')">
            
            <div class="absolute inset-0" style="background:linear-gradient(to right, rgba(28,18,8,0.85), rgba(44,24,16,0.6));"></div>

            <div class="relative z-10 text-center px-4 max-w-4xl mx-auto py-20">
                
                <div class="mb-8 flex justify-center">
                    <div style="background:#D4A853; border-radius:50%;" class="w-16 h-16 flex items-center justify-center shadow-lg shadow-black/20">
                        <svg class="w-8 h-8" style="color:#2C1810;" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/>
                        </svg>
                    </div>
                </div>

                
                <h1 class="text-4xl sm:text-5xl lg:text-7xl font-bold text-white mb-6 leading-tight tracking-wide" style="font-family:'Playfair Display',serif; text-shadow:0 4px 12px rgba(0,0,0,0.5);">
                    GREAT COFFEE,<br>EVERY DAY
                </h1>
                <p class="text-base sm:text-lg mb-10 max-w-2xl mx-auto leading-relaxed" style="color:rgba(255,255,255,0.8);">
                    Thưởng thức hương vị cà phê đích thực, pha chế từ những hạt cà phê được chọn lọc kỹ lưỡng, mang đến trải nghiệm tuyệt vời mỗi ngày.
                </p>

                
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <Link href="/menu"
                        class="px-8 py-3.5 text-sm font-semibold tracking-widest uppercase transition-all duration-200 hover:scale-105"
                        style="background:#D4A853; color:#2C1810; border-radius:2px; box-shadow:0 4px 12px rgba(0,0,0,0.2);">
                        ☕ Xem Menu
                    </Link>
                    <Link href="/menu"
                        class="px-8 py-3.5 border-2 text-white text-sm font-semibold tracking-widest uppercase transition-all duration-200 hover:bg-white/10"
                        style="border-color:rgba(255,255,255,0.5); border-radius:2px;">
                        Khám Phá
                    </Link>
                </div>
            </div>

            
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
                <svg class="w-6 h-6" style="color:rgba(255,255,255,0.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
            </div>
        </section>

        
        <section class="py-20 lg:py-28" style="background:#F2EBE0;">
            <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="max-w-2xl mb-16">
                    <p class="text-xs font-semibold tracking-widest uppercase mb-3" style="color:#A16A38;">Chất lượng hảo hạng</p>
                    <h2 class="text-3xl lg:text-4xl font-bold leading-tight" style="color:#2C1810; font-family:'Playfair Display',serif;">
                        Chúng Tôi Phục Vụ<br>Cà Phê Ngon Nhất.
                    </h2>
                </div>

                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <div v-for="(cat, index) in categories?.slice(0, 3)" :key="cat.id" class="group block cursor-pointer">
                        <Link :href="`/menu?category=${cat.slug}`" class="block h-full">
                            
                            <div class="aspect-[4/3] mb-6 overflow-hidden relative" style="border-radius:4px; box-shadow:0 10px 20px rgba(44,24,16,0.05);">
                                <div class="absolute inset-0 bg-[#2C1810]/10 group-hover:bg-transparent transition-all duration-500 z-10"></div>
                                <img :src="categoryImage(cat.slug)"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                            </div>

                            
                            <h3 class="text-base font-bold tracking-widest uppercase mb-3" style="color:#2C1810;">{{ cat.name }}</h3>
                            <p class="text-sm leading-relaxed mb-5" style="color:#5C3A1E;">
                                Khám phá các sản phẩm {{ cat.name.toLowerCase() }} được pha chế tỉ mỉ từ nguyên liệu chất lượng cao, mang lại trải nghiệm đáng nhớ.
                            </p>
                            <span class="text-sm font-semibold tracking-widest uppercase" style="color:#D4A853;">
                                Khám phá ngay <span class="inline-block transition-transform group-hover:translate-x-2">→</span>
                            </span>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="relative h-[55vh] bg-fixed bg-cover bg-center" style="background-image:url('/images/background_home.jpg')">
            <div class="absolute inset-0" style="background:rgba(28,18,8,0.7);"></div>
            <div class="relative z-10 h-full flex items-center justify-end">
                <div class="p-10 lg:p-14 mr-8 lg:mr-24 max-w-sm" style="background:rgba(44,24,16,0.85); backdrop-filter:blur(8px); border-left:3px solid #D4A853;">
                    <p class="text-xs font-semibold tracking-widest uppercase mb-3" style="color:#D4A853;">Nghệ thuật pha chế</p>
                    <h2 class="text-3xl lg:text-4xl font-bold text-white leading-tight" style="font-family:'Playfair Display',serif;">
                        TRẠM CÀ PHÊ
                    </h2>
                </div>
            </div>
        </section>

        
        <section class="relative py-20 lg:py-28" style="background:#2C1810;">
            <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <p class="text-xs font-semibold tracking-widest uppercase mb-4" style="color:#D4A853;">Thời gian hoạt động</p>
                        <h2 class="text-3xl lg:text-5xl font-bold text-white leading-tight mb-8" style="font-family:'Playfair Display',serif;">
                            Chúng Tôi Mở Cửa<br>Hàng Ngày Từ 07:00.<br>Hẹn Gặp Bạn!
                        </h2>
                        <p class="leading-relaxed max-w-lg mb-8" style="color:rgba(255,255,255,0.65);">
                            Ghé thăm chúng tôi để thưởng thức những ly cà phê thơm ngon nhất. Không gian ấm cúng, phục vụ tận tâm, mang đến cho bạn những giây phút thư giãn tuyệt vời sau những giờ làm việc mệt mỏi.
                        </p>
                        
                        <div class="p-6 inline-block" style="background:#1C1208; border:1px solid #5C3A1E; border-radius:4px;">
                            <div class="flex items-center gap-4 mb-3">
                                <span class="text-2xl">☕</span>
                                <div>
                                    <p class="text-sm font-semibold text-white">Thứ 2 - Thứ 6</p>
                                    <p class="text-xs" style="color:#D4A853;">07:00 AM - 10:00 PM</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-2xl">🌿</span>
                                <div>
                                    <p class="text-sm font-semibold text-white">Thứ 7 - Chủ Nhật</p>
                                    <p class="text-xs" style="color:#D4A853;">08:00 AM - 11:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        
                        <div class="absolute -inset-3 border-2 hidden lg:block" style="border-color:#5C3A1E;"></div>
                        <div class="aspect-[4/3] overflow-hidden relative z-10" style="box-shadow:0 20px 40px rgba(0,0,0,0.5);">
                            <img src="/images/background_home.jpg" class="w-full h-full object-cover transition duration-700 hover:scale-105" alt="Coffee shop interior" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="py-20 lg:py-28" style="background:#FAF6F0;">
            <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center mb-16">
                    <p class="text-xs font-semibold tracking-widest uppercase mb-3" style="color:#A16A38;">Sản phẩm nổi bật</p>
                    <h2 class="text-3xl lg:text-4xl font-bold" style="color:#2C1810; font-family:'Playfair Display',serif;">
                        Menu Đặc Biệt
                    </h2>
                    <div class="w-16 h-1 mx-auto mt-6" style="background:#D4A853;"></div>
                </div>

                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 lg:gap-8">
                    <Link
                        v-for="product in featuredProducts"
                        :key="product.id"
                        :href="`/menu/${product.slug}`"
                        class="group block p-4 transition-all duration-300"
                        style="background:white; border:1px solid #E8D9C5; border-radius:4px;"
                        onmouseover="this.style.borderColor='#D4A853'; this.style.boxShadow='0 10px 20px rgba(44,24,16,0.05)';"
                        onmouseout="this.style.borderColor='#E8D9C5'; this.style.boxShadow='none';"
                    >
                        <div class="aspect-square overflow-hidden mb-5 relative" style="background:#F8F5F0;">
                            <img v-if="product.image" :src="`/storage/${product.image}`"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <svg class="w-12 h-12 opacity-20" style="color:#2C1810;" fill="currentColor" viewBox="0 0 24 24"><path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/></svg>
                            </div>
                            
                            <div class="absolute top-3 right-3 px-2.5 py-1 text-xs font-bold" style="background:#2C1810; color:#D4A853; border-radius:2px;">
                                {{ formatCurrency(product.base_price) }}
                            </div>
                        </div>

                        <div class="text-center">
                            <p class="text-[10px] font-bold uppercase tracking-widest mb-1" style="color:#A16A38;">{{ product.category }}</p>
                            <h3 class="text-sm font-bold mb-2 transition-colors" style="color:#2C1810;">{{ product.name }}</h3>
                            <div v-if="product.avg_rating > 0" class="flex items-center justify-center gap-1">
                                <span style="color:#D4A853; font-size:12px;">★★★★★</span>
                                <span class="text-[10px] text-gray-400">({{ product.avg_rating }})</span>
                            </div>
                        </div>
                    </Link>
                </div>

                
                <div class="text-center mt-16">
                    <Link href="/menu"
                        class="inline-block px-10 py-3.5 text-sm font-semibold tracking-widest uppercase transition-all duration-200"
                        style="border:1px solid #2C1810; color:#2C1810; border-radius:2px;"
                        onmouseover="this.style.background='#2C1810'; this.style.color='white';"
                        onmouseout="this.style.background='transparent'; this.style.color='#2C1810';">
                        Xem Tất Cả Menu
                    </Link>
                </div>
            </div>
        </section>

        
        <section class="relative py-20 lg:py-24 bg-cover bg-center" style="background-image:url('/images/background_home.jpg')">
            <div class="absolute inset-0" style="background:rgba(28,18,8,0.9);"></div>
            <div class="relative z-10 text-center px-4 max-w-3xl mx-auto">
                <p class="text-xs font-semibold tracking-widest uppercase mb-4" style="color:#D4A853;">Specialty Coffee & More</p>
                <h2 class="text-3xl lg:text-5xl font-bold text-white mb-6" style="font-family:'Playfair Display',serif;">
                    TRẠM CÀ PHÊ
                </h2>
                <p class="mb-10 leading-relaxed" style="color:rgba(255,255,255,0.6);">
                    175 Tây Sơn, Đống Đa, Hà Nội<br>
                    Mở cửa hàng ngày từ 07:00 đến 22:00
                </p>
                <Link href="/menu"
                    class="inline-block px-10 py-3.5 text-sm font-semibold tracking-widest uppercase transition-all duration-200"
                    style="background:#D4A853; color:#2C1810; border-radius:2px; box-shadow:0 4px 12px rgba(0,0,0,0.2);"
                    onmouseover="this.style.transform='scale(1.05)'"
                    onmouseout="this.style.transform='scale(1)'">
                    Đặt Hàng Ngay
                </Link>
            </div>
        </section>
    </AppLayout>
</template>
