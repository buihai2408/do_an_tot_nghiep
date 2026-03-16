<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useFormatters } from '@/Composables/useFormatters';
import axios from 'axios';

const { formatCurrency } = useFormatters();
defineProps({ toppings: Object });

const showForm = ref(false);
const editingTopping = ref(null);
const form = ref({ name: '', price: '', is_active: true });

const openCreate = () => { editingTopping.value = null; form.value = { name: '', price: '', is_active: true }; showForm.value = true; };
const openEdit = (t) => { editingTopping.value = t; form.value = { name: t.name, price: t.price, is_active: t.is_active }; showForm.value = true; };

const submit = async () => {
    const data = { ...form.value, is_active: form.value.is_active ? 1 : 0 };
    if (editingTopping.value) {
        await axios.put(`/api/admin/toppings/${editingTopping.value.id}`, data);
    } else {
        await axios.post('/api/admin/toppings', data);
    }
    showForm.value = false;
    router.reload();
};

const deleteTopping = async (id) => { if (confirm('Xóa?')) { await axios.delete(`/api/admin/toppings/${id}`); router.reload(); } };
</script>

<template>
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Topping</h1>
            <button @click="openCreate" class="px-4 py-2 bg-amber-700 text-white rounded-lg hover:bg-amber-600">+ Thêm</button>
        </div>
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-gray-500">Tên</th><th class="px-6 py-3 text-right text-gray-500">Giá</th><th class="px-6 py-3 text-center text-gray-500">Trạng thái</th><th class="px-6 py-3 text-right text-gray-500">Thao tác</th></tr></thead>
                <tbody class="divide-y">
                    <tr v-for="t in toppings.data" :key="t.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium">{{ t.name }}</td>
                        <td class="px-6 py-4 text-right">{{ formatCurrency(t.price) }}</td>
                        <td class="px-6 py-4 text-center"><span :class="t.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-2 py-1 rounded-full text-xs font-semibold">{{ t.is_active ? 'Hoạt động' : 'Ẩn' }}</span></td>
                        <td class="px-6 py-4 text-right space-x-2"><button @click="openEdit(t)" class="text-amber-600">Sửa</button><button @click="deleteTopping(t.id)" class="text-red-500">Xóa</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-if="showForm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-6 max-w-sm w-full mx-4">
                <h3 class="text-lg font-bold mb-4">{{ editingTopping ? 'Sửa topping' : 'Thêm topping' }}</h3>
                <div class="space-y-3">
                    <div><label class="block text-sm font-medium mb-1">Tên</label><input v-model="form.name" class="w-full rounded-lg border-gray-300" /></div>
                    <div><label class="block text-sm font-medium mb-1">Giá</label><input v-model="form.price" type="number" class="w-full rounded-lg border-gray-300" /></div>
                    <label class="flex items-center"><input v-model="form.is_active" type="checkbox" class="rounded text-amber-600 mr-2" /> Hoạt động</label>
                </div>
                <div class="flex space-x-3 mt-4"><button @click="showForm = false" class="flex-1 px-4 py-2 border rounded-xl">Hủy</button><button @click="submit" class="flex-1 px-4 py-2 bg-amber-700 text-white rounded-xl">Lưu</button></div>
            </div>
        </div>
    </AdminLayout>
</template>
