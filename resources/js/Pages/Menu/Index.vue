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
        <section class="py-16 lg:py-20" style="background:#2C1810;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-xs font-semibold tracking-[0.3em] uppercase mb-3 flex items-center justify-center gap-3" style="color:#D4A853;">
                    <span class="w-6 h-px" style="background:#D4A853;"></span>
                    Thực đơn
                    <span class="w-6 h-px" style="background:#D4A853;"></span>
                </p>
                <h1 class="text-4xl lg:text-5xl font-bold text-white" style="font-family:'Playfair Display',serif;">Menu</h1>
                <p class="mt-3" style="color:rgba(255,255,255,0.6);">Khám phá thế giới cà phê đa dạng của chúng tôi</p>
            </div>
        </section>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Filters -->
            <div class="flex flex-col md:flex-row gap-4 mb-10 pb-8" style="border-bottom:1px solid #E8D9C5;">
                <div class="flex-1 relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-base">🔍</span>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Tìm kiếm sản phẩm..."
                        class="w-full border py-3 pl-10 pr-4 text-sm focus:outline-none"
                        style="border-color:#E8D9C5; background:#FAF6F0; color:#2C1810; border-radius:4px;"
                        onfocus="this.style.borderColor='#D4A853'; this.style.boxShadow='0 0 0 3px rgba(212,168,83,0.12)'"
                        onblur="this.style.borderColor='#E8D9C5'; this.style.boxShadow='none'"
                    />
                </div>
                <select v-model="selectedCategory"
                    class="border py-3 px-4 text-sm min-w-[180px] focus:outline-none"
                    style="border-color:#E8D9C5; background:#FAF6F0; color:#2C1810; border-radius:4px;"
                    onfocus="this.style.borderColor='#D4A853'" onblur="this.style.borderColor='#E8D9C5'">
                    <option value="">☕ Tất cả danh mục</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.slug">{{ cat.name }}</option>
                </select>
                <select v-model="selectedSort"
                    class="border py-3 px-4 text-sm min-w-[160px] focus:outline-none"
                    style="border-color:#E8D9C5; background:#FAF6F0; color:#2C1810; border-radius:4px;"
                    onfocus="this.style.borderColor='#D4A853'" onblur="this.style.borderColor='#E8D9C5'">
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
                    class="group block"
                >
                    <div class="aspect-square overflow-hidden mb-4 relative" style="background:#FAF6F0; border-radius:6px; border:1px solid #E8D9C5;">
                        <img v-if="product.image" :src="`/storage/${product.image}`"
                            class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-500" />
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16" style="color:#D4A853; opacity:0.5;" fill="currentColor" viewBox="0 0 24 24"><path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/></svg>
                        </div>
                        <!-- Quick-view overlay on hover -->
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300" style="background:rgba(44,24,16,0.4);">
                            <span class="text-xs font-semibold tracking-widest uppercase px-4 py-2" style="background:#D4A853; color:#2C1810; border-radius:2px;">Xem chi tiết</span>
                        </div>
                    </div>
                    <p class="text-xs uppercase tracking-wider mb-1" style="color:#B5A089;">{{ product.category }}</p>
                    <h3 class="text-sm font-semibold mb-1.5 transition-colors duration-200 line-clamp-1 group-hover:text-amber-800"
                        style="color:#2C1810;">{{ product.name }}</h3>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold" style="color:#2C1810;">{{ formatCurrency(product.base_price) }}</span>
                        <span v-if="product.avg_rating > 0" class="text-xs font-medium" style="color:#D4A853;">★ {{ product.avg_rating }}</span>
                    </div>
                    <div v-if="product.sizes?.length" class="mt-2 flex flex-wrap gap-1">
                        <span v-for="size in product.sizes" :key="size.id"
                            class="text-[10px] px-2 py-0.5"
                            style="background:#F2EBE0; color:#8B7355; border-radius:2px;">
                            {{ size.name }}
                        </span>
                    </div>
                </Link>
            </div>

            <!-- Empty state -->
            <div v-if="products.data.length === 0" class="text-center py-20">
                <div class="text-5xl mb-4">☕</div>
                <p class="text-lg font-semibold mb-2" style="color:#2C1810;">Không tìm thấy sản phẩm</p>
                <p class="text-sm" style="color:#8B7355;">Hãy thử tìm kiếm với từ khóa khác.</p>
            </div>

            <!-- Pagination -->
            <div v-if="products.links?.length > 3" class="flex justify-center mt-12 space-x-1">
                <template v-for="link in products.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                        class="px-4 py-2 text-sm border transition-all duration-200"
                        :style="link.active
                            ? 'background:#2C1810; color:white; border-color:#2C1810; border-radius:4px;'
                            : 'background:white; color:#8B7355; border-color:#E8D9C5; border-radius:4px;'"
                        onmouseover="if(!this.style.backgroundColor.includes('44')) { this.style.borderColor='#D4A853'; this.style.color='#2C1810'; }"
                        onmouseout="if(!this.style.backgroundColor.includes('44')) { this.style.borderColor='#E8D9C5'; this.style.color='#8B7355'; }"
                    />
                    <span v-else v-html="link.label" class="px-4 py-2 text-sm" style="color:#B5A089;" />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
