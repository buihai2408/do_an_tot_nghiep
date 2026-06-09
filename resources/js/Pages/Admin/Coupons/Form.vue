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
                    <div>
                        <label class="block text-sm font-medium mb-1">Mã *</label>
                        <input v-model="form.code" :class="errors.code ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]'" class="w-full rounded uppercase" />
                        <p v-if="errors.code" class="text-red-500 text-xs mt-1">{{ errors.code[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tên *</label>
                        <input v-model="form.name" :class="errors.name ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]'" class="w-full rounded" />
                        <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name[0] }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Loại</label>
                        <select v-model="form.type" :class="errors.type ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]'" class="w-full rounded">
                            <option value="percentage">Phần trăm (%)</option>
                            <option value="fixed">Số tiền cố định</option>
                        </select>
                        <p v-if="errors.type" class="text-red-500 text-xs mt-1">{{ errors.type[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Giá trị *</label>
                        <input v-model="form.value" type="number" :class="errors.value ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]'" class="w-full rounded" />
                        <p v-if="errors.value" class="text-red-500 text-xs mt-1">{{ errors.value[0] }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Đơn tối thiểu</label>
                        <input v-model="form.min_order_amount" type="number" :class="errors.min_order_amount ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]'" class="w-full rounded" />
                        <p v-if="errors.min_order_amount" class="text-red-500 text-xs mt-1">{{ errors.min_order_amount[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Giảm tối đa</label>
                        <input v-model="form.max_discount" type="number" :class="errors.max_discount ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]'" class="w-full rounded" />
                        <p v-if="errors.max_discount" class="text-red-500 text-xs mt-1">{{ errors.max_discount[0] }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Giới hạn dùng</label>
                        <input v-model="form.usage_limit" type="number" :class="errors.usage_limit ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]'" class="w-full rounded" />
                        <p v-if="errors.usage_limit" class="text-red-500 text-xs mt-1">{{ errors.usage_limit[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Bắt đầu</label>
                        <input v-model="form.starts_at" type="datetime-local" :class="errors.starts_at ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]'" class="w-full rounded" />
                        <p v-if="errors.starts_at" class="text-red-500 text-xs mt-1">{{ errors.starts_at[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Kết thúc</label>
                        <input v-model="form.expires_at" type="datetime-local" :class="errors.expires_at ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]'" class="w-full rounded" />
                        <p v-if="errors.expires_at" class="text-red-500 text-xs mt-1">{{ errors.expires_at[0] }}</p>
                    </div>
                </div>
                <label class="flex items-center"><input v-model="form.is_active" type="checkbox" class="rounded text-[#D4A853] mr-2" /> Hoạt động</label>
                <button @click="submit" class="px-6 py-3 bg-amber-700 text-white rounded-xl font-semibold hover:bg-amber-600">{{ coupon ? 'Cập nhật' : 'Tạo mới' }}</button>
            </div>
        </div>
    </AdminLayout>
</template>
