<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({ coupon: { type: Object, default: null } });

const form = ref({
    code: props.coupon?.code || '',
    name: props.coupon?.name || '',
    type: props.coupon?.type || 'percentage',
    value: props.coupon?.value || '',
    min_order_amount: props.coupon?.min_order_amount || 0,
    max_discount: props.coupon?.max_discount || '',
    usage_limit: props.coupon?.usage_limit || '',
    starts_at: props.coupon?.starts_at?.substring(0, 16) || '',
    expires_at: props.coupon?.expires_at?.substring(0, 16) || '',
    is_active: props.coupon?.is_active ?? true,
});
const errors = ref({});

const submit = async () => {
    try {
        if (props.coupon) {
            await axios.put(`/api/admin/coupons/${props.coupon.id}`, { ...form.value, is_active: form.value.is_active ? 1 : 0 });
        } else {
            await axios.post('/api/admin/coupons', { ...form.value, is_active: form.value.is_active ? 1 : 0 });
        }
        router.visit('/admin/coupons');
    } catch (e) {
        errors.value = e.response?.data?.errors || {};
    }
};
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-bold text-[#1a1a1a] font-serif mb-6">{{ coupon ? 'Sửa mã giảm giá' : 'Thêm mã giảm giá' }}</h1>
        <div class="bg-white rounded border border-[#E8D9C5] p-6 shadow-sm max-w-2xl">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium mb-1">Mã *</label><input v-model="form.code" class="w-full rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853] uppercase" /></div>
                    <div><label class="block text-sm font-medium mb-1">Tên *</label><input v-model="form.name" class="w-full rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]" /></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium mb-1">Loại</label>
                        <select v-model="form.type" class="w-full rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]">
                            <option value="percentage">Phần trăm (%)</option>
                            <option value="fixed">Số tiền cố định</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-medium mb-1">Giá trị *</label><input v-model="form.value" type="number" class="w-full rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]" /></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium mb-1">Đơn tối thiểu</label><input v-model="form.min_order_amount" type="number" class="w-full rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]" /></div>
                    <div><label class="block text-sm font-medium mb-1">Giảm tối đa</label><input v-model="form.max_discount" type="number" class="w-full rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]" /></div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div><label class="block text-sm font-medium mb-1">Giới hạn dùng</label><input v-model="form.usage_limit" type="number" class="w-full rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]" /></div>
                    <div><label class="block text-sm font-medium mb-1">Bắt đầu</label><input v-model="form.starts_at" type="datetime-local" class="w-full rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]" /></div>
                    <div><label class="block text-sm font-medium mb-1">Kết thúc</label><input v-model="form.expires_at" type="datetime-local" class="w-full rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]" /></div>
                </div>
                <label class="flex items-center"><input v-model="form.is_active" type="checkbox" class="rounded text-[#D4A853] mr-2" /> Hoạt động</label>
                <button @click="submit" class="px-6 py-3 bg-amber-700 text-white rounded-xl font-semibold hover:bg-amber-600">{{ coupon ? 'Cập nhật' : 'Tạo mới' }}</button>
            </div>
        </div>
    </AdminLayout>
</template>
