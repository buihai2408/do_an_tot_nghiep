<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

defineProps({ sizes: Array });

const showForm = ref(false);
const editingSize = ref(null);
const form = ref({ name: '', label: '', sort_order: 0 });

const openCreate = () => { editingSize.value = null; form.value = { name: '', label: '', sort_order: 0 }; showForm.value = true; };
const openEdit = (s) => { editingSize.value = s; form.value = { name: s.name, label: s.label, sort_order: s.sort_order }; showForm.value = true; };

const submit = async () => {
    if (editingSize.value) { await axios.put(`/api/admin/sizes/${editingSize.value.id}`, form.value); }
    else { await axios.post('/api/admin/sizes', form.value); }
    showForm.value = false; router.reload();
};
const deleteSize = async (id) => { if (confirm('Xóa?')) { await axios.delete(`/api/admin/sizes/${id}`); router.reload(); } };
</script>

<template>
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Kích thước</h1>
            <button @click="openCreate" class="px-4 py-2 bg-amber-700 text-white rounded-lg hover:bg-amber-600">+ Thêm</button>
        </div>
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left">Tên</th><th class="px-6 py-3 text-left">Nhãn</th><th class="px-6 py-3 text-center">Thứ tự</th><th class="px-6 py-3 text-right">Thao tác</th></tr></thead>
                <tbody class="divide-y">
                    <tr v-for="s in sizes" :key="s.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-bold">{{ s.name }}</td>
                        <td class="px-6 py-4">{{ s.label }}</td>
                        <td class="px-6 py-4 text-center">{{ s.sort_order }}</td>
                        <td class="px-6 py-4 text-right space-x-2"><button @click="openEdit(s)" class="text-amber-600">Sửa</button><button @click="deleteSize(s.id)" class="text-red-500">Xóa</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-if="showForm" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-6 max-w-sm w-full mx-4">
                <h3 class="text-lg font-bold mb-4">{{ editingSize ? 'Sửa' : 'Thêm' }}</h3>
                <div class="space-y-3">
                    <div><label class="block text-sm font-medium mb-1">Tên (S/M/L)</label><input v-model="form.name" class="w-full rounded-lg border-gray-300" /></div>
                    <div><label class="block text-sm font-medium mb-1">Nhãn</label><input v-model="form.label" class="w-full rounded-lg border-gray-300" /></div>
                    <div><label class="block text-sm font-medium mb-1">Thứ tự</label><input v-model.number="form.sort_order" type="number" class="w-full rounded-lg border-gray-300" /></div>
                </div>
                <div class="flex space-x-3 mt-4"><button @click="showForm = false" class="flex-1 px-4 py-2 border rounded-xl">Hủy</button><button @click="submit" class="flex-1 px-4 py-2 bg-amber-700 text-white rounded-xl">Lưu</button></div>
            </div>
        </div>
    </AdminLayout>
</template>
