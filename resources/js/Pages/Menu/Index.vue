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
        <!-- Page Header -->
        <section class="bg-[#1a1a1a] py-16 lg:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl lg:text-5xl font-bold text-white" style="font-family: 'Playfair Display', serif;">Menu</h1>
                <p class="text-gray-400 mt-3">Khám phá thế giới cà phê đa dạng của chúng tôi</p>
            </div>
        </section>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Filters -->
            <div class="flex flex-col md:flex-row gap-4 mb-10 pb-8 border-b border-gray-200">
                <div class="flex-1">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Tìm kiếm sản phẩm..."
                        class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] text-sm py-3 px-4"
                    />
                </div>
                <select v-model="selectedCategory" class="border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] text-sm py-3 px-4 min-w-[180px]">
                    <option value="">Tất cả danh mục</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.slug">{{ cat.name }}</option>
                </select>
                <select v-model="selectedSort" class="border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] text-sm py-3 px-4 min-w-[160px]">
                    <option value="">Mới nhất</option>
                    <option value="price_asc">Giá tăng dần</option>
                    <option value="price_desc">Giá giảm dần</option>
                    <option value="name">Tên A-Z</option>
                </select>
            </div>

            <!-- Products grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 lg:gap-8">
                <Link
                    v-for="product in products.data"
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
                    <h3 class="text-sm font-semibold text-[#1a1a1a] mb-1 group-hover:text-amber-700 transition line-clamp-1">{{ product.name }}</h3>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-[#1a1a1a]">{{ formatCurrency(product.base_price) }}</span>
                        <span v-if="product.avg_rating > 0" class="text-xs text-amber-600">★ {{ product.avg_rating }}</span>
                    </div>
                    <div v-if="product.sizes?.length" class="mt-2 flex flex-wrap gap-1">
                        <span v-for="size in product.sizes" :key="size.id" class="text-[10px] bg-gray-100 text-gray-500 px-2 py-0.5">
                            {{ size.name }}
                        </span>
                    </div>
                </Link>
            </div>

            <div v-if="products.data.length === 0" class="text-center py-20 text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <p class="text-lg">Không tìm thấy sản phẩm nào.</p>
            </div>

            <!-- Pagination -->
            <div v-if="products.links?.length > 3" class="flex justify-center mt-12 space-x-1">
                <template v-for="link in products.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                        class="px-4 py-2 text-sm border transition"
                        :class="link.active ? 'bg-[#1a1a1a] text-white border-[#1a1a1a]' : 'bg-white text-gray-600 border-gray-300 hover:border-[#1a1a1a] hover:text-[#1a1a1a]'"
                    />
                    <span v-else v-html="link.label" class="px-4 py-2 text-sm text-gray-300" />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
