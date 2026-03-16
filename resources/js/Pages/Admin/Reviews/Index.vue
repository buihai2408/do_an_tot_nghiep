<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

defineProps({ reviews: Object });

const approveReview = async (id) => { await axios.put(`/api/admin/reviews/${id}/approve`); router.reload(); };
const deleteReview = async (id) => { if (confirm('Xóa?')) { await axios.delete(`/api/admin/reviews/${id}`); router.reload(); } };
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Đánh giá</h1>
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left">Khách</th><th class="px-6 py-3 text-left">Sản phẩm</th><th class="px-6 py-3 text-center">Sao</th><th class="px-6 py-3 text-left">Nhận xét</th><th class="px-6 py-3 text-center">Trạng thái</th><th class="px-6 py-3 text-right">Thao tác</th></tr></thead>
                <tbody class="divide-y">
                    <tr v-for="review in reviews.data" :key="review.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ review.user?.name }}</td>
                        <td class="px-6 py-4">{{ review.product?.name }}</td>
                        <td class="px-6 py-4 text-center text-yellow-500">{{ '⭐'.repeat(review.rating) }}</td>
                        <td class="px-6 py-4 text-gray-500 max-w-xs truncate">{{ review.comment }}</td>
                        <td class="px-6 py-4 text-center">
                            <span :class="review.is_approved ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'" class="px-2 py-1 rounded-full text-xs font-semibold">{{ review.is_approved ? 'Đã duyệt' : 'Chờ duyệt' }}</span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button v-if="!review.is_approved" @click="approveReview(review.id)" class="text-green-600">Duyệt</button>
                            <button @click="deleteReview(review.id)" class="text-red-500">Xóa</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
