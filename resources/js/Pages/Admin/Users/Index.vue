<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({ users: Object, filters: Object });
const search = ref(props.filters?.search || '');

const applySearch = () => router.get('/admin/users', { search: search.value || undefined }, { preserveState: true });

const updateRole = async (user, role) => {
    await axios.put(`/api/admin/users/${user.id}`, { role });
    router.reload();
};
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Người dùng</h1>
        <div class="bg-white rounded-2xl shadow-md p-4 mb-6">
            <input v-model="search" @keyup.enter="applySearch" placeholder="Tìm theo tên, email..." class="w-full rounded-lg border-gray-300" />
        </div>
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left">Tên</th><th class="px-6 py-3 text-left">Email</th><th class="px-6 py-3 text-left">SĐT</th><th class="px-6 py-3 text-center">Vai trò</th><th class="px-6 py-3 text-right">Ngày tạo</th></tr></thead>
                <tbody class="divide-y">
                    <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium">{{ user.name }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ user.email }}</td>
                        <td class="px-6 py-4">{{ user.phone }}</td>
                        <td class="px-6 py-4 text-center">
                            <select :value="user.role" @change="updateRole(user, $event.target.value)" class="rounded-lg border-gray-300 text-sm">
                                <option value="customer">Khách hàng</option>
                                <option value="staff">Nhân viên</option>
                                <option value="admin">Quản trị</option>
                            </select>
                        </td>
                        <td class="px-6 py-4 text-right text-gray-500">{{ new Date(user.created_at).toLocaleDateString('vi-VN') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
