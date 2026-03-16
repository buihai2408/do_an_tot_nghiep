<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useFormatters } from '@/Composables/useFormatters';
import axios from 'axios';

const { formatCurrency } = useFormatters();

const props = defineProps({ products: Object, categories: Array, filters: Object });
const search = ref(props.filters?.search || '');

const applySearch = () => {
    router.get('/admin/products', { search: search.value || undefined, category: props.filters?.category }, { preserveState: true });
};

const deleteProduct = async (id) => {
    if (!confirm('Xác nhận xóa?')) return;
    await axios.delete(`/api/admin/products/${id}`);
    router.reload();
};
</script>

<template>
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Quản lý sản phẩm</h1>
            <Link href="/admin/products/create" class="px-4 py-2 bg-amber-700 text-white rounded-lg hover:bg-amber-600 transition">+ Thêm sản phẩm</Link>
        </div>

        <div class="bg-white rounded-2xl shadow-md p-4 mb-6">
            <input v-model="search" @keyup.enter="applySearch" type="text" placeholder="Tìm kiếm..." class="w-full rounded-lg border-gray-300" />
        </div>

        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-gray-500">Sản phẩm</th>
                        <th class="px-6 py-3 text-left text-gray-500">Danh mục</th>
                        <th class="px-6 py-3 text-right text-gray-500">Giá</th>
                        <th class="px-6 py-3 text-center text-gray-500">Trạng thái</th>
                        <th class="px-6 py-3 text-right text-gray-500">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <img
                                    v-if="product.primary_image?.path"
                                    :src="`/storage/${product.primary_image.path}`"
                                    class="w-10 h-10 rounded-lg object-cover"
                                />
                                <div v-else class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">☕</div>
                                <div>
                                    <p class="font-medium">{{ product.name }}</p>
                                    <p v-if="product.is_featured" class="text-xs text-amber-600">⭐ Nổi bật</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ product.category?.name }}</td>
                        <td class="px-6 py-4 text-right font-medium">{{ formatCurrency(product.base_price) }}</td>
                        <td class="px-6 py-4 text-center">
                            <span :class="product.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-2 py-1 rounded-full text-xs font-semibold">
                                {{ product.is_active ? 'Đang bán' : 'Ẩn' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <Link :href="`/admin/products/${product.id}/edit`" class="text-amber-600 hover:text-amber-800">Sửa</Link>
                            <button @click="deleteProduct(product.id)" class="text-red-500 hover:text-red-700">Xóa</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
