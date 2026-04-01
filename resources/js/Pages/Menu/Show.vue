<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { useFormatters } from '@/Composables/useFormatters';
import { useCart } from '@/Composables/useCart';
import { useToast } from '@/Composables/useToast';

const { formatCurrency } = useFormatters();
const { addToCart, loading } = useCart();
const { success, error: toastError } = useToast();

const props = defineProps({
    product: Object,
    ice_levels: Array,
    sugar_levels: Array,
});

const currentImageIndex = ref(0);
const allImages = computed(() => {
    if (props.product.images?.length) {
        return props.product.images.map(img => `/storage/${img.path}`);
    }
    return [];
});
const hasMultipleImages = computed(() => allImages.value.length > 1);
const prevImage = () => { currentImageIndex.value = (currentImageIndex.value - 1 + allImages.value.length) % allImages.value.length; };
const nextImage = () => { currentImageIndex.value = (currentImageIndex.value + 1) % allImages.value.length; };

const selectedSize = ref(props.product.sizes?.[0]?.id || null);
const selectedIce = ref('normal');
const selectedSugar = ref('normal');
const selectedToppings = ref([]);
const quantity = ref(1);

const currentPrice = computed(() => {
    const size = props.product.sizes?.find(s => s.id === selectedSize.value);
    const basePrice = Number(size?.price ?? props.product.base_price) || 0;
    const toppingTotal = selectedToppings.value.reduce((sum, id) => {
        const topping = props.product.toppings?.find(t => t.id === id);
        return sum + (Number(topping?.price) || 0);
    }, 0);
    return (basePrice + toppingTotal) * quantity.value;
});

const handleAddToCart = async () => {
    try {
        await addToCart({
            product_id: props.product.id,
            size_id: selectedSize.value,
            ice_level: selectedIce.value,
            sugar_level: selectedSugar.value,
            topping_ids: selectedToppings.value,
            quantity: quantity.value,
        });
        success('Đã thêm vào giỏ hàng!');
    } catch (e) {
        toastError('Không thể thêm vào giỏ hàng.');
    }
};

const toggleTopping = (id) => {
    const index = selectedToppings.value.indexOf(id);
    if (index > -1) {
        selectedToppings.value.splice(index, 1);
    } else {
        selectedToppings.value.push(id);
    }
};
</script>

<template>
    <AppLayout>
        <!-- Breadcrumb -->
        <div class="bg-[#f8f5f0] py-4">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="text-sm text-gray-500">
                    <a href="/" class="hover:text-[#1a1a1a] transition">Trang chủ</a>
                    <span class="mx-2">/</span>
                    <a href="/menu" class="hover:text-[#1a1a1a] transition">Menu</a>
                    <span class="mx-2">/</span>
                    <span class="text-[#1a1a1a]">{{ product.name }}</span>
                </nav>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Image Gallery -->
                <div class="relative">
                    <div class="aspect-square bg-[#f8f5f0] overflow-hidden">
                        <template v-if="allImages.length">
                            <img :src="allImages[currentImageIndex]" class="w-full h-full object-cover" />
                            <template v-if="hasMultipleImages">
                                <button @click="prevImage" class="absolute left-3 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-[#1a1a1a] w-10 h-10 flex items-center justify-center transition shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" /></svg>
                                </button>
                                <button @click="nextImage" class="absolute right-3 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-[#1a1a1a] w-10 h-10 flex items-center justify-center transition shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" /></svg>
                                </button>
                            </template>
                        </template>
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <svg class="w-24 h-24 text-amber-300" fill="currentColor" viewBox="0 0 24 24"><path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/></svg>
                        </div>
                    </div>
                    <div v-if="hasMultipleImages" class="flex gap-2 mt-3">
                        <button
                            v-for="(img, i) in allImages"
                            :key="i"
                            @click="currentImageIndex = i"
                            class="w-16 h-16 overflow-hidden border-2 transition"
                            :class="i === currentImageIndex ? 'border-[#1a1a1a]' : 'border-transparent opacity-60 hover:opacity-100'"
                        >
                            <img :src="img" class="w-full h-full object-cover" />
                        </button>
                    </div>
                </div>

                <!-- Details -->
                <div>
                    <p class="text-xs font-semibold tracking-widest uppercase text-gray-400 mb-2">{{ product.category?.name }}</p>
                    <h1 class="text-3xl lg:text-4xl font-bold text-[#1a1a1a] mb-3" style="font-family: 'Playfair Display', serif;">{{ product.name }}</h1>
                    <p class="text-gray-500 leading-relaxed mb-6">{{ product.description }}</p>

                    <div v-if="product.avg_rating > 0" class="flex items-center space-x-2 mb-6 pb-6 border-b border-gray-200">
                        <div class="flex text-amber-500">
                            <span v-for="i in 5" :key="i" class="text-lg">{{ i <= Math.round(product.avg_rating) ? '★' : '☆' }}</span>
                        </div>
                        <span class="text-sm text-gray-500">({{ product.review_count }} đánh giá)</span>
                    </div>

                    <!-- Size Selector -->
                    <div v-if="product.sizes?.length" class="mb-6">
                        <h3 class="text-xs font-semibold tracking-widest uppercase text-[#1a1a1a] mb-3">Kích thước</h3>
                        <div class="flex gap-3">
                            <button
                                v-for="size in product.sizes"
                                :key="size.id"
                                @click="selectedSize = size.id"
                                :class="selectedSize === size.id
                                    ? 'bg-[#1a1a1a] text-white border-[#1a1a1a]'
                                    : 'bg-white text-[#1a1a1a] border-gray-300 hover:border-[#1a1a1a]'"
                                class="px-5 py-3 border text-sm font-medium transition flex flex-col items-center min-w-[80px]"
                            >
                                <span class="font-bold">{{ size.name }}</span>
                                <span class="text-[10px] mt-0.5 opacity-70">{{ formatCurrency(size.price) }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Ice Level -->
                    <div class="mb-6">
                        <h3 class="text-xs font-semibold tracking-widest uppercase text-[#1a1a1a] mb-3">Mức đá</h3>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="level in ice_levels"
                                :key="level.value"
                                @click="selectedIce = level.value"
                                :class="selectedIce === level.value
                                    ? 'bg-[#1a1a1a] text-white'
                                    : 'bg-[#f8f5f0] text-[#1a1a1a] hover:bg-gray-200'"
                                class="px-4 py-2 text-sm font-medium transition"
                            >
                                {{ level.label }}
                            </button>
                        </div>
                    </div>

                    <!-- Sugar Level -->
                    <div class="mb-6">
                        <h3 class="text-xs font-semibold tracking-widest uppercase text-[#1a1a1a] mb-3">Mức đường</h3>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="level in sugar_levels"
                                :key="level.value"
                                @click="selectedSugar = level.value"
                                :class="selectedSugar === level.value
                                    ? 'bg-[#1a1a1a] text-white'
                                    : 'bg-[#f8f5f0] text-[#1a1a1a] hover:bg-gray-200'"
                                class="px-4 py-2 text-sm font-medium transition"
                            >
                                {{ level.label }}
                            </button>
                        </div>
                    </div>

                    <!-- Toppings -->
                    <div v-if="product.toppings?.length" class="mb-8">
                        <h3 class="text-xs font-semibold tracking-widest uppercase text-[#1a1a1a] mb-3">Topping</h3>
                        <div class="space-y-2">
                            <label
                                v-for="topping in product.toppings"
                                :key="topping.id"
                                class="flex items-center justify-between p-3.5 border cursor-pointer transition"
                                :class="selectedToppings.includes(topping.id) ? 'border-[#1a1a1a] bg-[#f8f5f0]' : 'border-gray-200 hover:border-gray-400'"
                            >
                                <div class="flex items-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedToppings.includes(topping.id)"
                                        @change="toggleTopping(topping.id)"
                                        class="rounded-sm text-[#1a1a1a] focus:ring-[#1a1a1a] border-gray-400"
                                    />
                                    <span class="ml-3 text-sm">{{ topping.name }}</span>
                                </div>
                                <span class="text-sm font-medium text-gray-600">+{{ formatCurrency(topping.price) }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Quantity & Add to cart -->
                    <div class="flex items-center gap-4">
                        <div class="flex items-center border border-gray-300">
                            <button @click="quantity > 1 && quantity--" class="px-4 py-3 text-[#1a1a1a] hover:bg-gray-100 transition text-lg">−</button>
                            <span class="px-4 py-3 font-semibold min-w-[3rem] text-center text-sm">{{ quantity }}</span>
                            <button @click="quantity++" class="px-4 py-3 text-[#1a1a1a] hover:bg-gray-100 transition text-lg">+</button>
                        </div>
                        <button
                            @click="handleAddToCart"
                            :disabled="loading"
                            class="flex-1 bg-[#1a1a1a] text-white py-3.5 text-sm font-semibold tracking-wider uppercase hover:bg-[#333] transition disabled:opacity-50"
                        >
                            {{ loading ? 'Đang thêm...' : `Thêm vào giỏ — ${formatCurrency(currentPrice)}` }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Reviews -->
            <div v-if="product.reviews?.length" class="mt-16 pt-12 border-t border-gray-200">
                <h2 class="text-2xl font-bold text-[#1a1a1a] mb-8" style="font-family: 'Playfair Display', serif;">Đánh giá từ khách hàng</h2>
                <div class="space-y-6">
                    <div v-for="review in product.reviews" :key="review.id" class="pb-6 border-b border-gray-100 last:border-0">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-[#1a1a1a] text-white text-xs font-bold rounded-full flex items-center justify-center">
                                    {{ review.user_name?.charAt(0)?.toUpperCase() }}
                                </div>
                                <span class="font-semibold text-sm text-[#1a1a1a]">{{ review.user_name }}</span>
                                <div class="flex text-amber-500 text-sm">
                                    <span v-for="i in 5" :key="i">{{ i <= review.rating ? '★' : '☆' }}</span>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">{{ review.created_at }}</span>
                        </div>
                        <p v-if="review.comment" class="text-sm text-gray-600 leading-relaxed ml-11">{{ review.comment }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
