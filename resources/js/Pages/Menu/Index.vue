<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { useFormatters } from '@/Composables/useFormatters';

const { formatCurrency } = useFormatters();

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const selectedCategory = ref(props.filters?.category || '');
const selectedSort = ref(props.filters?.sort || '');

const applyFilters = () => {
    router.get('/menu', {
        search: search.value || undefined,
        category: selectedCategory.value || undefined,
        sort: selectedSort.value || undefined,
    }, { preserveState: true, preserveScroll: true });
};

let debounceTimer;
watch(search, () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(applyFilters, 300);
});
watch([selectedCategory, selectedSort], applyFilters);
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold text-amber-900 mb-8">Thực đơn</h1>

            <!-- Filters -->
            <div class="bg-white rounded-2xl p-6 shadow-md mb-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Tìm kiếm sản phẩm..."
                        class="w-full rounded-lg border-amber-300 focus:border-amber-500 focus:ring-amber-500"
                    />
                    <select v-model="selectedCategory" class="rounded-lg border-amber-300 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">Tất cả danh mục</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.slug">{{ cat.name }}</option>
                    </select>
                    <select v-model="selectedSort" class="rounded-lg border-amber-300 focus:border-amber-500 focus:ring-amber-500">
                        <option value="">Mới nhất</option>
                        <option value="price_asc">Giá tăng dần</option>
                        <option value="price_desc">Giá giảm dần</option>
                        <option value="name">Tên A-Z</option>
                    </select>
                </div>
            </div>

            <!-- Products grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <Link
                    v-for="product in products.data"
                    :key="product.id"
                    :href="`/menu/${product.slug}`"
                    class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl hover:-translate-y-1 transition-all group"
                >
                    <div class="h-48 bg-amber-100 flex items-center justify-center overflow-hidden">
                        <img v-if="product.image" :src="`/storage/${product.image}`" class="w-full h-full object-cover group-hover:scale-105 transition-transform" />
                        <span v-else class="text-6xl">☕</span>
                    </div>
                    <div class="p-4">
                        <p class="text-xs text-amber-600 mb-1">{{ product.category }}</p>
                        <h3 class="font-semibold text-amber-900 mb-2 line-clamp-1">{{ product.name }}</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-amber-700">{{ formatCurrency(product.base_price) }}</span>
                            <span v-if="product.avg_rating > 0" class="text-sm text-amber-600">⭐ {{ product.avg_rating }}</span>
                        </div>
                        <div v-if="product.sizes?.length" class="mt-2 flex flex-wrap gap-1">
                            <span v-for="size in product.sizes" :key="size.id" class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">
                                {{ size.name }}
                            </span>
                        </div>
                    </div>
                </Link>
            </div>

            <div v-if="products.data.length === 0" class="text-center py-16 text-gray-500">
                <p class="text-lg">Không tìm thấy sản phẩm nào.</p>
            </div>

            <!-- Pagination -->
            <div v-if="products.links?.length > 3" class="flex justify-center mt-8 space-x-1">
                <template v-for="link in products.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                        class="px-4 py-2 rounded-lg text-sm"
                        :class="link.active ? 'bg-amber-700 text-white' : 'bg-white text-amber-700 hover:bg-amber-100'"
                    />
                    <span v-else v-html="link.label" class="px-4 py-2 text-sm text-gray-400" />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
