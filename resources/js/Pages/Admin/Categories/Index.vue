<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

defineProps({ categories: Object });

const showForm = ref(false);
const editingCategory = ref(null);
const form = ref({ name: '', sort_order: 0, is_active: true });

const openCreate = () => { editingCategory.value = null; form.value = { name: '', sort_order: '', is_active: true }; showForm.value = true; };
const openEdit = (cat) => { editingCategory.value = cat; form.value = { name: cat.name, sort_order: cat.sort_order, is_active: cat.is_active }; showForm.value = true; };

const submit = async () => {
    const formData = new FormData();
    Object.entries(form.value).forEach(([k, v]) => formData.append(k, v === true ? '1' : v === false ? '0' : v ?? ''));

    try {
        if (editingCategory.value) {
            await axios.post(`/api/admin/categories/${editingCategory.value.id}`, formData);
        } else {
            await axios.post('/api/admin/categories', formData);
        }
        showForm.value = false;
        router.reload();
    } catch (e) {
        alert(e.response?.data?.message || 'Có lỗi xảy ra');
    }
};

const deleteCategory = async (id) => {
    if (!confirm('Xác nhận xóa?')) return;
    try {
        await axios.delete(`/api/admin/categories/${id}`);
        router.reload();
    } catch (e) {
        alert(e.response?.data?.message || 'Không thể xóa');
    }
};
</script>

<template>
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-[#1a1a1a] font-serif">Quản lý danh mục</h1>
            <button @click="openCreate" class="px-4 py-2 bg-[#2C1810] text-white rounded hover:bg-[#5C3A1E] transition transition">+ Thêm danh mục</button>
        </div>

        <div class="bg-white rounded border border-[#E8D9C5] overflow-hidden shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-[#FAF6F0]">
                    <tr>
                        <th class="px-6 py-3 text-left text-gray-500">Tên</th>
                        <th class="px-6 py-3 text-left text-gray-500">Slug</th>
                        <th class="px-6 py-3 text-center text-gray-500">Thứ tự</th>
                        <th class="px-6 py-3 text-center text-gray-500">Trạng thái</th>
                        <th class="px-6 py-3 text-right text-gray-500">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="cat in categories.data" :key="cat.id" class="hover:bg-[#FAF6F0] transition">
                        <td class="px-6 py-4 font-medium">{{ cat.name }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ cat.slug }}</td>
                        <td class="px-6 py-4 text-center">{{ cat.sort_order }}</td>
                        <td class="px-6 py-4 text-center">
                            <span :class="cat.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-2 py-1 rounded-full text-xs font-semibold">
                                {{ cat.is_active ? 'Hoạt động' : 'Ẩn' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button @click="openEdit(cat)" class="text-[#D4A853] hover:text-[#2C1810] transition font-medium">Sửa</button>
                            <button @click="deleteCategory(cat.id)" class="text-red-500 hover:text-red-700">Xóa</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <Pagination :links="categories.links" />

        <!-- Modal -->
        <div v-if="showForm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-bold mb-4">{{ editingCategory ? 'Sửa danh mục' : 'Thêm danh mục' }}</h3>
                <div class="space-y-4">
                    <div><label class="block text-sm font-medium mb-1">Tên</label><input v-model="form.name" class="w-full rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]" /></div>
                    <div><label class="block text-sm font-medium mb-1">Thứ tự</label><input v-model.number="form.sort_order" type="number" placeholder="Để trống để tự động gán" class="w-full rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]" /></div>
                    <label class="flex items-center"><input v-model="form.is_active" type="checkbox" class="rounded text-[#D4A853] mr-2" /> Hoạt động</label>
                </div>
                <div class="flex space-x-3 mt-6">
                    <button @click="showForm = false" class="flex-1 px-4 py-2 border rounded-xl">Hủy</button>
                    <button @click="submit" class="flex-1 px-4 py-2 bg-amber-700 text-white rounded-xl">Lưu</button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
