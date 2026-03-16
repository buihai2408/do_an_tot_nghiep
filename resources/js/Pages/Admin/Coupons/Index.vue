<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { useFormatters } from '@/Composables/useFormatters';
import axios from 'axios';

const { formatCurrency, formatDate } = useFormatters();
defineProps({ coupons: Object });

const deleteCoupon = async (id) => {
    if (!confirm('Xác nhận xóa?')) return;
    await axios.delete(`/api/admin/coupons/${id}`);
    router.reload();
};
</script>

<template>
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Mã giảm giá</h1>
            <Link href="/admin/coupons/create" class="px-4 py-2 bg-amber-700 text-white rounded-lg hover:bg-amber-600">+ Thêm mã</Link>
        </div>
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-gray-500">Mã</th>
                        <th class="px-6 py-3 text-left text-gray-500">Tên</th>
                        <th class="px-6 py-3 text-center text-gray-500">Loại</th>
                        <th class="px-6 py-3 text-right text-gray-500">Giá trị</th>
                        <th class="px-6 py-3 text-center text-gray-500">Đã dùng</th>
                        <th class="px-6 py-3 text-center text-gray-500">Trạng thái</th>
                        <th class="px-6 py-3 text-right text-gray-500">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="coupon in coupons.data" :key="coupon.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono font-bold">{{ coupon.code }}</td>
                        <td class="px-6 py-4">{{ coupon.name }}</td>
                        <td class="px-6 py-4 text-center">{{ coupon.type === 'percentage' ? '%' : 'VNĐ' }}</td>
                        <td class="px-6 py-4 text-right">{{ coupon.type === 'percentage' ? coupon.value + '%' : formatCurrency(coupon.value) }}</td>
                        <td class="px-6 py-4 text-center">{{ coupon.used_count }}/{{ coupon.usage_limit || '∞' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span :class="coupon.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="px-2 py-1 rounded-full text-xs font-semibold">
                                {{ coupon.is_active ? 'Hoạt động' : 'Tắt' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <Link :href="`/admin/coupons/${coupon.id}/edit`" class="text-amber-600 hover:text-amber-800">Sửa</Link>
                            <button @click="deleteCoupon(coupon.id)" class="text-red-500 hover:text-red-700">Xóa</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
