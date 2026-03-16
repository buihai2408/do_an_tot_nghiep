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
    if (props.product.image) {
        return [`/storage/${props.product.image}`];
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
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2">
                    <!-- Image Gallery -->
                    <div class="h-80 md:h-full bg-amber-100 relative overflow-hidden">
                        <template v-if="allImages.length">
                            <img :src="allImages[currentImageIndex]" class="w-full h-full object-cover" />
                            <template v-if="hasMultipleImages">
                                <button @click="prevImage" class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white w-10 h-10 rounded-full flex items-center justify-center transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                                </button>
                                <button @click="nextImage" class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white w-10 h-10 rounded-full flex items-center justify-center transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </button>
                                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5">
                                    <button
                                        v-for="(_, i) in allImages"
                                        :key="i"
                                        @click="currentImageIndex = i"
                                        class="w-2.5 h-2.5 rounded-full transition"
                                        :class="i === currentImageIndex ? 'bg-white' : 'bg-white/50'"
                                    />
                                </div>
                            </template>
                        </template>
                        <div v-else class="w-full h-full flex items-center justify-center text-8xl">☕</div>
                    </div>

                    <!-- Details -->
                    <div class="p-8">
                        <p class="text-sm text-amber-600 mb-1">{{ product.category?.name }}</p>
                        <h1 class="text-3xl font-bold text-amber-900 mb-2">{{ product.name }}</h1>
                        <p class="text-gray-600 mb-6">{{ product.description }}</p>

                        <div v-if="product.avg_rating > 0" class="flex items-center space-x-2 mb-6">
                            <span class="text-yellow-500">⭐ {{ product.avg_rating }}</span>
                            <span class="text-sm text-gray-500">({{ product.review_count }} đánh giá)</span>
                        </div>

                        <!-- Size Selector -->
                        <div v-if="product.sizes?.length" class="mb-6">
                            <h3 class="font-semibold text-amber-900 mb-3">Kích thước</h3>
                            <div class="flex space-x-3">
                                <button
                                    v-for="size in product.sizes"
                                    :key="size.id"
                                    @click="selectedSize = size.id"
                                    :class="selectedSize === size.id
                                        ? 'bg-amber-700 text-white border-amber-700'
                                        : 'bg-white text-amber-700 border-amber-300 hover:border-amber-500'"
                                    class="px-6 py-3 rounded-xl border-2 font-medium transition flex flex-col items-center"
                                >
                                    <span class="font-bold">{{ size.name }}</span>
                                    <span class="text-xs mt-1">{{ formatCurrency(size.price) }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Ice Level -->
                        <div class="mb-6">
                            <h3 class="font-semibold text-amber-900 mb-3">Mức đá</h3>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    v-for="level in ice_levels"
                                    :key="level.value"
                                    @click="selectedIce = level.value"
                                    :class="selectedIce === level.value
                                        ? 'bg-amber-700 text-white'
                                        : 'bg-amber-100 text-amber-700 hover:bg-amber-200'"
                                    class="px-4 py-2 rounded-lg text-sm font-medium transition"
                                >
                                    {{ level.label }}
                                </button>
                            </div>
                        </div>

                        <!-- Sugar Level -->
                        <div class="mb-6">
                            <h3 class="font-semibold text-amber-900 mb-3">Mức đường</h3>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    v-for="level in sugar_levels"
                                    :key="level.value"
                                    @click="selectedSugar = level.value"
                                    :class="selectedSugar === level.value
                                        ? 'bg-amber-700 text-white'
                                        : 'bg-amber-100 text-amber-700 hover:bg-amber-200'"
                                    class="px-4 py-2 rounded-lg text-sm font-medium transition"
                                >
                                    {{ level.label }}
                                </button>
                            </div>
                        </div>

                        <!-- Toppings -->
                        <div v-if="product.toppings?.length" class="mb-6">
                            <h3 class="font-semibold text-amber-900 mb-3">Topping</h3>
                            <div class="space-y-2">
                                <label
                                    v-for="topping in product.toppings"
                                    :key="topping.id"
                                    class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition"
                                    :class="selectedToppings.includes(topping.id) ? 'border-amber-500 bg-amber-50' : 'border-gray-200 hover:border-amber-300'"
                                >
                                    <div class="flex items-center">
                                        <input
                                            type="checkbox"
                                            :checked="selectedToppings.includes(topping.id)"
                                            @change="toggleTopping(topping.id)"
                                            class="rounded text-amber-600 focus:ring-amber-500"
                                        />
                                        <span class="ml-3">{{ topping.name }}</span>
                                    </div>
                                    <span class="text-amber-600 font-medium">+{{ formatCurrency(topping.price) }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Quantity & Add to cart -->
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center border rounded-xl">
                                <button @click="quantity > 1 && quantity--" class="px-4 py-3 text-amber-700 hover:bg-amber-50 rounded-l-xl">-</button>
                                <span class="px-4 py-3 font-semibold min-w-[3rem] text-center">{{ quantity }}</span>
                                <button @click="quantity++" class="px-4 py-3 text-amber-700 hover:bg-amber-50 rounded-r-xl">+</button>
                            </div>
                            <button
                                @click="handleAddToCart"
                                :disabled="loading"
                                class="flex-1 bg-amber-700 text-white py-3 rounded-xl font-semibold hover:bg-amber-600 transition disabled:opacity-50"
                            >
                                {{ loading ? 'Đang thêm...' : `Thêm vào giỏ - ${formatCurrency(currentPrice)}` }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews -->
            <div v-if="product.reviews?.length" class="mt-8 bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-amber-900 mb-6">Đánh giá</h2>
                <div class="space-y-4">
                    <div v-for="review in product.reviews" :key="review.id" class="border-b pb-4">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <span class="font-semibold text-amber-900">{{ review.user_name }}</span>
                                <span class="text-yellow-500 ml-2">{{ '⭐'.repeat(review.rating) }}</span>
                            </div>
                            <span class="text-sm text-gray-500">{{ review.created_at }}</span>
                        </div>
                        <p v-if="review.comment" class="text-gray-600">{{ review.comment }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
