<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    status: Number,
});

const title = computed(() => {
    return {
        403: 'Không có quyền truy cập',
        404: 'Không tìm thấy trang',
        500: 'Lỗi hệ thống',
        503: 'Đang bảo trì',
    }[props.status] || 'Có lỗi xảy ra';
});

const description = computed(() => {
    return {
        403: 'Bạn không có quyền truy cập trang này.',
        404: 'Trang bạn tìm kiếm không tồn tại hoặc đã bị xóa.',
        500: 'Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau.',
        503: 'Hệ thống đang bảo trì. Vui lòng quay lại sau.',
    }[props.status] || 'Đã xảy ra lỗi không xác định.';
});

const emoji = computed(() => {
    return { 403: '🔒', 404: '🔍', 500: '⚠️', 503: '🔧' }[props.status] || '❌';
});
</script>

<template>
    <div class="min-h-screen bg-amber-50 flex items-center justify-center px-4">
        <div class="text-center max-w-md">
            <p class="text-8xl mb-6">{{ emoji }}</p>
            <h1 class="text-6xl font-bold text-amber-900 mb-4">{{ status }}</h1>
            <h2 class="text-2xl font-semibold text-amber-800 mb-4">{{ title }}</h2>
            <p class="text-gray-600 mb-8">{{ description }}</p>
            <div class="space-x-4">
                <Link href="/" class="inline-block bg-amber-700 text-white px-6 py-3 rounded-xl font-semibold hover:bg-amber-600 transition">
                    Về trang chủ
                </Link>
                <button @click="$inertia.visit(window.history.back())" class="inline-block border-2 border-amber-700 text-amber-700 px-6 py-3 rounded-xl font-semibold hover:bg-amber-50 transition">
                    Quay lại
                </button>
            </div>
        </div>
    </div>
</template>
