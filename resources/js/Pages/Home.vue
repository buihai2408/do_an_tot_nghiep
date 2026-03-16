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
        <section class="relative text-white py-24 bg-cover bg-center bg-no-repeat" style="background-image: url('/images/background_home.jpg')">
            <div class="absolute inset-0 bg-black/50"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-2xl">
                    <h1 class="text-5xl font-bold mb-6">Thưởng thức cà phê <br>đích thực</h1>
                    <p class="text-xl text-amber-200 mb-8">Pha chế từ những hạt cà phê được chọn lọc kỹ lưỡng, mang đến hương vị tuyệt vời nhất.</p>
                    <Link href="/menu" class="inline-block bg-white text-amber-900 px-8 py-3 rounded-full font-semibold hover:bg-amber-100 transition shadow-lg">
                        Xem thực đơn →
                    </Link>
                </div>
            </div>
        </section>

        <!-- Categories -->
        <section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-amber-900 text-center mb-12">Danh mục</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-6">
                <Link
                    v-for="cat in categories"
                    :key="cat.id"
                    :href="`/menu?category=${cat.slug}`"
                    class="bg-white rounded-2xl p-6 text-center shadow-md hover:shadow-xl hover:-translate-y-1 transition-all"
                >
                    <div class="w-16 h-16 mx-auto mb-3 bg-amber-100 rounded-full flex items-center justify-center text-2xl">☕</div>
                    <h3 class="font-semibold text-amber-900">{{ cat.name }}</h3>
                </Link>
            </div>
        </section>

        <!-- Featured Products -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-amber-900 text-center mb-12">Sản phẩm nổi bật</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <Link
                        v-for="product in featuredProducts"
                        :key="product.id"
                        :href="`/menu/${product.slug}`"
                        class="bg-amber-50 rounded-2xl overflow-hidden shadow-md hover:shadow-xl hover:-translate-y-1 transition-all group"
                    >
                        <div class="h-48 bg-amber-200 flex items-center justify-center overflow-hidden">
                            <img v-if="product.image" :src="`/storage/${product.image}`" class="w-full h-full object-cover group-hover:scale-105 transition-transform" />
                            <span v-else class="text-6xl">☕</span>
                        </div>
                        <div class="p-4">
                            <p class="text-xs text-amber-600 mb-1">{{ product.category }}</p>
                            <h3 class="font-semibold text-amber-900 mb-2">{{ product.name }}</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-amber-700">{{ formatCurrency(product.base_price) }}</span>
                                <span v-if="product.avg_rating > 0" class="text-sm text-amber-600">
                                    ⭐ {{ product.avg_rating }}
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>
                <div class="text-center mt-10">
                    <Link href="/menu" class="inline-block bg-amber-700 text-white px-8 py-3 rounded-full font-semibold hover:bg-amber-600 transition">
                        Xem tất cả →
                    </Link>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
