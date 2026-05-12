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
        <div style="background:#FAF6F0; border-bottom:1px solid #E8D9C5;" class="py-4">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="text-sm" style="color:#8B7355;">
                    <a href="/" class="transition hover:text-amber-700" style="color:#8B7355;">Trang chủ</a>
                    <span class="mx-2">·</span>
                    <a href="/menu" class="transition hover:text-amber-700" style="color:#8B7355;">Menu</a>
                    <span class="mx-2">·</span>
                    <span style="color:#2C1810; font-weight:600;">{{ product.name }}</span>
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
                    <p class="text-xs font-semibold tracking-widest uppercase mb-2" style="color:#D4A853;">{{ product.category?.name }}</p>
                    <h1 class="text-3xl lg:text-4xl font-bold mb-3" style="color:#2C1810; font-family:'Playfair Display',serif;">{{ product.name }}</h1>
                    <p class="leading-relaxed mb-6" style="color:#8B7355;">{{ product.description }}</p>

                    <div v-if="product.avg_rating > 0" class="flex items-center space-x-2 mb-6 pb-6" style="border-bottom:1px solid #E8D9C5;">
                        <div class="flex" style="color:#D4A853;">
                            <span v-for="i in 5" :key="i" class="text-lg">{{ i <= Math.round(product.avg_rating) ? '★' : '☆' }}</span>
                        </div>
                        <span class="text-sm" style="color:#8B7355;">({{ product.review_count }} đánh giá)</span>
                    </div>

                    <!-- Size Selector -->
                    <div v-if="product.sizes?.length" class="mb-6">
                        <h3 class="text-xs font-semibold tracking-widest uppercase mb-3" style="color:#2C1810;">☕ Kích thước</h3>
                        <div class="flex gap-3">
                            <button
                                v-for="size in product.sizes"
                                :key="size.id"
                                @click="selectedSize = size.id"
                                :style="selectedSize === size.id
                                    ? 'background:#2C1810; color:white; border-color:#2C1810;'
                                    : 'background:white; color:#2C1810; border-color:#E8D9C5;'"
                                class="px-5 py-3 border text-sm font-medium transition-all duration-200 flex flex-col items-center min-w-[80px] hover:border-amber-600"
                                style="border-radius:4px;"
                            >
                                <span class="font-bold">{{ size.name }}</span>
                                <span class="text-[10px] mt-0.5 opacity-70">{{ formatCurrency(size.price) }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Ice Level -->
                    <div class="mb-6">
                        <h3 class="text-xs font-semibold tracking-widest uppercase mb-3" style="color:#2C1810;">🧊 Mức đá</h3>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="level in ice_levels"
                                :key="level.value"
                                @click="selectedIce = level.value"
                                :style="selectedIce === level.value
                                    ? 'background:#2C1810; color:white;'
                                    : 'background:#FAF6F0; color:#2C1810;'"
                                class="px-4 py-2 text-sm font-medium transition-all duration-200 hover:opacity-80"
                                style="border-radius:4px;"
                            >
                                {{ level.label }}
                            </button>
                        </div>
                    </div>

                    <!-- Sugar Level -->
                    <div class="mb-6">
                        <h3 class="text-xs font-semibold tracking-widest uppercase mb-3" style="color:#2C1810;">🍯 Mức đường</h3>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="level in sugar_levels"
                                :key="level.value"
                                @click="selectedSugar = level.value"
                                :style="selectedSugar === level.value
                                    ? 'background:#2C1810; color:white;'
                                    : 'background:#FAF6F0; color:#2C1810;'"
                                class="px-4 py-2 text-sm font-medium transition-all duration-200 hover:opacity-80"
                                style="border-radius:4px;"
                            >
                                {{ level.label }}
                            </button>
                        </div>
                    </div>

                    <!-- Toppings -->
                    <div v-if="product.toppings?.length" class="mb-8">
                        <h3 class="text-xs font-semibold tracking-widest uppercase mb-3" style="color:#2C1810;">✨ Topping</h3>
                        <div class="space-y-2">
                            <label
                                v-for="topping in product.toppings"
                                :key="topping.id"
                                class="flex items-center justify-between p-3.5 border cursor-pointer transition-all duration-200"
                                :style="selectedToppings.includes(topping.id)
                                    ? 'border-color:#2C1810; background:#FAF6F0;'
                                    : 'border-color:#E8D9C5;'"
                                style="border-radius:4px;"
                            >
                                <div class="flex items-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedToppings.includes(topping.id)"
                                        @change="toggleTopping(topping.id)"
                                        class="rounded-sm border-gray-400"
                                        style="accent-color:#2C1810;"
                                    />
                                    <span class="ml-3 text-sm" style="color:#2C1810;">{{ topping.name }}</span>
                                </div>
                                <span class="text-sm font-medium" style="color:#8B7355;">+{{ formatCurrency(topping.price) }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Quantity & Add to cart -->
                    <div class="flex items-center gap-4">
                        <div class="flex items-center border" style="border-color:#E8D9C5; border-radius:4px;">
                            <button @click="quantity > 1 && quantity--"
                                class="px-4 py-3 text-lg transition hover:opacity-60"
                                style="color:#2C1810;">−</button>
                            <span class="px-4 py-3 font-semibold min-w-[3rem] text-center text-sm" style="color:#2C1810;">{{ quantity }}</span>
                            <button @click="quantity++"
                                class="px-4 py-3 text-lg transition hover:opacity-60"
                                style="color:#2C1810;">+</button>
                        </div>
                        <button
                            @click="handleAddToCart"
                            :disabled="loading"
                            class="flex-1 py-3.5 text-sm font-semibold tracking-wider uppercase transition-all duration-200 disabled:opacity-50"
                            style="background:#2C1810; color:white; border-radius:4px; box-shadow:0 4px 15px rgba(44,24,16,0.25);"
                            onmouseover="if(!this.disabled) this.style.background='#D4A853'; if(!this.disabled) this.style.color='#2C1810';"
                            onmouseout="if(!this.disabled) this.style.background='#2C1810'; if(!this.disabled) this.style.color='white';"
                        >
                            {{ loading ? '⏳ Đang thêm...' : `🛒 Thêm vào giỏ — ${formatCurrency(currentPrice)}` }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Reviews -->
            <div v-if="product.reviews?.length" class="mt-16 pt-12" style="border-top:1px solid #E8D9C5;">
                <h2 class="text-2xl font-bold mb-8" style="color:#2C1810; font-family:'Playfair Display',serif;">Đánh giá từ khách hàng</h2>
                <div class="space-y-6">
                    <div v-for="review in product.reviews" :key="review.id" class="pb-6 last:border-0" style="border-bottom:1px solid #F2EBE0;">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 text-white text-xs font-bold rounded-full flex items-center justify-center" style="background:#2C1810;">
                                    {{ review.user_name?.charAt(0)?.toUpperCase() }}
                                </div>
                                <span class="font-semibold text-sm" style="color:#2C1810;">{{ review.user_name }}</span>
                                <div class="flex text-sm" style="color:#D4A853;">
                                    <span v-for="i in 5" :key="i">{{ i <= review.rating ? '★' : '☆' }}</span>
                                </div>
                            </div>
                            <span class="text-xs" style="color:#B5A089;">{{ review.created_at }}</span>
                        </div>
                        <p v-if="review.comment" class="text-sm leading-relaxed ml-11" style="color:#8B7355;">{{ review.comment }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
