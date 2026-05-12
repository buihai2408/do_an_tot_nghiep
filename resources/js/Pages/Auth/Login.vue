<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    login: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Đăng nhập" />

        <div class="bg-white p-8 lg:p-10 border border-[#E8D9C5] rounded-lg shadow-[0_4px_20px_rgba(44,24,16,0.08)]">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-[#2C1810] font-playfair mb-1">Đăng nhập</h2>
                <p class="text-sm text-[#8B7355]">Chào mừng bạn trở lại! ☕</p>
            </div>

            <div v-if="status" class="mb-6 text-sm font-medium text-[#065F46] bg-[#ECFDF5] border border-[#A7F3D0] rounded-md p-3">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold tracking-widest uppercase text-[#8B7355] mb-2">Email hoặc số điện thoại</label>
                    <input
                        v-model="form.login"
                        type="text"
                        required
                        autofocus
                        autocomplete="username"
                        class="w-full text-sm py-3 text-[#2C1810] border-[#E8D9C5] rounded focus:border-[#D4A853] focus:ring focus:ring-[#D4A853]/20 transition"
                        placeholder="email@example.com hoặc 0901234567"
                    />
                    <p v-if="form.errors.login" class="text-red-500 text-xs mt-1.5">{{ form.errors.login }}</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold tracking-widest uppercase text-[#8B7355] mb-2">Mật khẩu</label>
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="w-full text-sm py-3 text-[#2C1810] border-[#E8D9C5] rounded focus:border-[#D4A853] focus:ring focus:ring-[#D4A853]/20 transition"
                        placeholder="••••••••"
                    />
                    <p v-if="form.errors.password" class="text-red-500 text-xs mt-1.5">{{ form.errors.password }}</p>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center cursor-pointer">
                        <input v-model="form.remember" type="checkbox" class="rounded-sm border-[#E8D9C5] text-[#2C1810] focus:ring-[#D4A853]" />
                        <span class="ml-2 text-sm text-[#8B7355]">Ghi nhớ đăng nhập</span>
                    </label>
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-[#D4A853] hover:text-[#2C1810] transition"
                    >
                        Quên mật khẩu?
                    </Link>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full py-3.5 bg-[#2C1810] text-white text-sm font-semibold tracking-wider uppercase rounded hover:bg-[#5C3A1E] transition disabled:opacity-50 shadow-[0_4px_15px_rgba(44,24,16,0.25)] hover:shadow-none"
                >
                    {{ form.processing ? '⏳ Đang đăng nhập...' : 'Đăng nhập ☕' }}
                </button>
            </form>

            <p class="text-center text-sm text-[#8B7355] mt-8">
                Chưa có tài khoản?
                <Link :href="route('register')" class="text-[#D4A853] font-semibold hover:text-[#2C1810] transition">Đăng ký ngay</Link>
            </p>
        </div>
    </GuestLayout>
</template>
