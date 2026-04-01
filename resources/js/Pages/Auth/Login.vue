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

        <div class="bg-white p-8 lg:p-10 shadow-sm">
            <h2 class="text-2xl font-bold text-[#1a1a1a] mb-1" style="font-family: 'Playfair Display', serif;">Đăng nhập</h2>
            <p class="text-sm text-gray-400 mb-8">Chào mừng bạn trở lại!</p>

            <div v-if="status" class="mb-6 text-sm font-medium text-green-700 bg-green-50 p-3 border border-green-200">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Email hoặc số điện thoại</label>
                    <input
                        v-model="form.login"
                        type="text"
                        required
                        autofocus
                        autocomplete="username"
                        class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] py-3 text-sm"
                        placeholder="email@example.com hoặc 0901234567"
                    />
                    <p v-if="form.errors.login" class="text-red-500 text-xs mt-1.5">{{ form.errors.login }}</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Mật khẩu</label>
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] py-3 text-sm"
                        placeholder="••••••••"
                    />
                    <p v-if="form.errors.password" class="text-red-500 text-xs mt-1.5">{{ form.errors.password }}</p>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input v-model="form.remember" type="checkbox" class="rounded-sm text-[#1a1a1a] focus:ring-[#1a1a1a] border-gray-400" />
                        <span class="ml-2 text-sm text-gray-500">Ghi nhớ đăng nhập</span>
                    </label>
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-gray-500 hover:text-[#1a1a1a] transition"
                    >
                        Quên mật khẩu?
                    </Link>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full py-3.5 bg-[#1a1a1a] text-white text-sm font-semibold tracking-wider uppercase hover:bg-[#333] transition disabled:opacity-50"
                >
                    {{ form.processing ? 'Đang đăng nhập...' : 'Đăng nhập' }}
                </button>
            </form>

            <p class="text-center text-sm text-gray-400 mt-8">
                Chưa có tài khoản?
                <Link :href="route('register')" class="text-[#1a1a1a] font-semibold hover:underline">Đăng ký ngay</Link>
            </p>
        </div>
    </GuestLayout>
</template>
