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
        <div style="background:#FAF6F0; border-bottom:1px solid #E8D9C5;" class="py-4 lg:py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center text-sm font-medium tracking-wide" style="color:#8B7355;">
                    <a href="/" class="transition-colors hover:text-[#D4A853]">Trang chủ</a>
                    <span class="mx-3 opacity-50">/</span>
                    <a href="/menu" class="transition-colors hover:text-[#D4A853]">Thực đơn</a>
                    <span class="mx-3 opacity-50">/</span>
                    <span style="color:#2C1810;">{{ product.name }}</span>
                </nav>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">
                
                <!-- Image Gallery (Left Side) -->
                <div class="lg:col-span-5 relative">
                    <div class="sticky top-28">
                        <div class="aspect-[4/5] bg-[#F8F5F0] overflow-hidden rounded-xl border" style="border-color:#E8D9C5; box-shadow:0 20px 40px rgba(44,24,16,0.06);">
                            <template v-if="allImages.length">
                                <img :src="allImages[currentImageIndex]" class="w-full h-full object-cover transition-transform duration-700 hover:scale-105" />
                                <template v-if="hasMultipleImages">
                                    <button @click="prevImage" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center rounded-full transition-all duration-300 shadow-md" style="background:rgba(255,255,255,0.9); color:#2C1810;" onmouseover="this.style.background='#2C1810'; this.style.color='white'" onmouseout="this.style.background='rgba(255,255,255,0.9)'; this.style.color='#2C1810'">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                                    </button>
                                    <button @click="nextImage" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 flex items-center justify-center rounded-full transition-all duration-300 shadow-md" style="background:rgba(255,255,255,0.9); color:#2C1810;" onmouseover="this.style.background='#2C1810'; this.style.color='white'" onmouseout="this.style.background='rgba(255,255,255,0.9)'; this.style.color='#2C1810'">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                    </button>
                                </template>
                            </template>
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <svg class="w-32 h-32 opacity-20" style="color:#2C1810;" fill="currentColor" viewBox="0 0 24 24"><path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/></svg>
                            </div>
                        </div>
                        <!-- Thumbnails -->
                        <div v-if="hasMultipleImages" class="flex gap-4 mt-6 justify-center">
                            <button
                                v-for="(img, i) in allImages"
                                :key="i"
                                @click="currentImageIndex = i"
                                class="w-20 h-20 overflow-hidden rounded-lg border-2 transition-all duration-300"
                                :style="i === currentImageIndex ? 'border-color:#D4A853; box-shadow:0 4px 10px rgba(212,168,83,0.3);' : 'border-color:transparent; opacity:0.6;'"
                            >
                                <img :src="img" class="w-full h-full object-cover" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product Details (Right Side) -->
                <div class="lg:col-span-7 flex flex-col justify-center">
                    <div class="mb-8">
                        <span class="inline-block px-3 py-1 text-xs font-bold tracking-widest uppercase rounded-full mb-4" style="background:#F2EBE0; color:#A16A38;">
                            {{ product.category?.name }}
                        </span>
                        <h1 class="text-4xl lg:text-5xl font-bold mb-4 leading-tight" style="color:#2C1810; font-family:'Playfair Display',serif;">
                            {{ product.name }}
                        </h1>
                        <p class="text-base lg:text-lg leading-relaxed mb-6" style="color:#6B5340;">
                            {{ product.description }}
                        </p>

                        <div v-if="product.avg_rating > 0" class="flex items-center space-x-3 mb-8 pb-8" style="border-bottom:1px solid #E8D9C5;">
                            <div class="flex items-center text-xl" style="color:#D4A853;">
                                <span v-for="i in 5" :key="i">{{ i <= Math.round(product.avg_rating) ? '★' : '☆' }}</span>
                            </div>
                            <span class="text-sm font-medium" style="color:#A16A38;">({{ product.review_count }} đánh giá từ khách hàng)</span>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <!-- Size Selector -->
                        <div v-if="product.sizes?.length">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-sm font-bold tracking-widest uppercase flex items-center gap-2" style="color:#2C1810;">
                                    <span class="text-xl">☕</span> Kích thước
                                </h3>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <button
                                    v-for="size in product.sizes"
                                    :key="size.id"
                                    @click="selectedSize = size.id"
                                    class="relative px-4 py-4 rounded-xl border-2 transition-all duration-300 flex flex-col items-center justify-center overflow-hidden group"
                                    :style="selectedSize === size.id
                                        ? 'border-color:#2C1810; background:#FAF6F0;'
                                        : 'border-color:#E8D9C5; background:white;'"
                                >
                                    <div v-if="selectedSize === size.id" class="absolute inset-0 bg-[#2C1810] opacity-5"></div>
                                    <span class="font-bold text-base mb-1" :style="selectedSize === size.id ? 'color:#2C1810;' : 'color:#6B5340;'">{{ size.name }}</span>
                                    <span class="text-xs font-semibold" :style="selectedSize === size.id ? 'color:#D4A853;' : 'color:#A16A38;'">{{ formatCurrency(size.price) }}</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Ice Level -->
                            <div>
                                <h3 class="text-sm font-bold tracking-widest uppercase flex items-center gap-2 mb-4" style="color:#2C1810;">
                                    <span class="text-xl">🧊</span> Mức đá
                                </h3>
                                <div class="flex flex-wrap gap-3">
                                    <button
                                        v-for="level in ice_levels"
                                        :key="level.value"
                                        @click="selectedIce = level.value"
                                        class="px-5 py-2.5 text-sm font-bold rounded-full transition-all duration-200 border-2"
                                        :style="selectedIce === level.value
                                            ? 'background:#2C1810; color:white; border-color:#2C1810;'
                                            : 'background:white; color:#6B5340; border-color:#E8D9C5;'"
                                    >
                                        {{ level.label }}
                                    </button>
                                </div>
                            </div>

                            <!-- Sugar Level -->
                            <div>
                                <h3 class="text-sm font-bold tracking-widest uppercase flex items-center gap-2 mb-4" style="color:#2C1810;">
                                    <span class="text-xl">🍯</span> Mức đường
                                </h3>
                                <div class="flex flex-wrap gap-3">
                                    <button
                                        v-for="level in sugar_levels"
                                        :key="level.value"
                                        @click="selectedSugar = level.value"
                                        class="px-5 py-2.5 text-sm font-bold rounded-full transition-all duration-200 border-2"
                                        :style="selectedSugar === level.value
                                            ? 'background:#2C1810; color:white; border-color:#2C1810;'
                                            : 'background:white; color:#6B5340; border-color:#E8D9C5;'"
                                    >
                                        {{ level.label }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Toppings -->
                        <div v-if="product.toppings?.length">
                            <h3 class="text-sm font-bold tracking-widest uppercase flex items-center gap-2 mb-4" style="color:#2C1810;">
                                <span class="text-xl">✨</span> Topping thêm
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label
                                    v-for="topping in product.toppings"
                                    :key="topping.id"
                                    class="flex items-center justify-between p-4 rounded-xl border-2 cursor-pointer transition-all duration-300 group"
                                    :style="selectedToppings.includes(topping.id)
                                        ? 'border-color:#2C1810; background:#FAF6F0;'
                                        : 'border-color:#E8D9C5; background:white;'"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="relative flex items-center justify-center w-5 h-5 rounded border-2 transition-colors"
                                            :style="selectedToppings.includes(topping.id) ? 'border-color:#2C1810; background:#2C1810;' : 'border-color:#A16A38;'">
                                            <svg v-if="selectedToppings.includes(topping.id)" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <!-- Checkbox ẩn -->
                                        <input
                                            type="checkbox"
                                            class="hidden"
                                            :checked="selectedToppings.includes(topping.id)"
                                            @change="toggleTopping(topping.id)"
                                        />
                                        <span class="text-sm font-semibold transition-colors" :style="selectedToppings.includes(topping.id) ? 'color:#2C1810;' : 'color:#6B5340;'">{{ topping.name }}</span>
                                    </div>
                                    <span class="text-sm font-bold" style="color:#D4A853;">+{{ formatCurrency(topping.price) }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Add to Cart Bar -->
                        <div class="mt-10 pt-8" style="border-top:1px solid #E8D9C5;">
                            <div class="flex flex-col sm:flex-row items-center gap-6">
                                <!-- Quantity -->
                                <div class="flex items-center h-14 bg-white rounded-xl border-2 overflow-hidden shrink-0" style="border-color:#E8D9C5;">
                                    <button @click="quantity > 1 && quantity--" class="w-14 h-full flex items-center justify-center text-xl transition-colors hover:bg-[#FAF6F0]" style="color:#2C1810;">−</button>
                                    <span class="w-12 h-full flex items-center justify-center font-bold text-lg" style="color:#2C1810;">{{ quantity }}</span>
                                    <button @click="quantity++" class="w-14 h-full flex items-center justify-center text-xl transition-colors hover:bg-[#FAF6F0]" style="color:#2C1810;">+</button>
                                </div>
                                
                                <!-- Submit -->
                                <button
                                    @click="handleAddToCart"
                                    :disabled="loading"
                                    class="w-full h-14 flex items-center justify-center gap-3 text-sm font-bold tracking-widest uppercase transition-all duration-300 rounded-xl disabled:opacity-70 disabled:cursor-not-allowed group"
                                    style="background:#2C1810; color:white; box-shadow:0 10px 30px rgba(44,24,16,0.3);"
                                    onmouseover="if(!this.disabled) { this.style.transform='translateY(-2px)'; this.style.boxShadow='0 15px 35px rgba(44,24,16,0.4)'; }"
                                    onmouseout="if(!this.disabled) { this.style.transform='none'; this.style.boxShadow='0 10px 30px rgba(44,24,16,0.3)'; }"
                                >
                                    <svg v-if="!loading" class="w-5 h-5 transition-transform group-hover:-rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" /></svg>
                                    <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span>{{ loading ? 'Đang thêm...' : `Thêm vào giỏ — ${formatCurrency(currentPrice)}` }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews Section -->
            <div v-if="product.reviews?.length" class="mt-24 pt-16" style="border-top:1px solid #E8D9C5;">
                <div class="max-w-4xl mx-auto">
                    <h2 class="text-3xl font-bold mb-12 text-center" style="color:#2C1810; font-family:'Playfair Display',serif;">Đánh giá từ khách hàng</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div v-for="review in product.reviews" :key="review.id" class="p-8 rounded-2xl bg-white border shadow-sm transition-transform hover:-translate-y-1" style="border-color:#E8D9C5;">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 text-white text-lg font-bold rounded-full flex items-center justify-center shadow-inner" style="background:linear-gradient(135deg, #2C1810, #5C3A1E);">
                                        {{ review.user_name?.charAt(0)?.toUpperCase() }}
                                    </div>
                                    <div>
                                        <span class="font-bold block" style="color:#2C1810;">{{ review.user_name }}</span>
                                        <span class="text-xs" style="color:#A16A38;">{{ review.created_at }}</span>
                                    </div>
                                </div>
                                <div class="flex text-sm" style="color:#D4A853;">
                                    <span v-for="i in 5" :key="i">{{ i <= review.rating ? '★' : '☆' }}</span>
                                </div>
                            </div>
                            <p v-if="review.comment" class="text-sm leading-relaxed mt-4 italic" style="color:#6B5340;">"{{ review.comment }}"</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
