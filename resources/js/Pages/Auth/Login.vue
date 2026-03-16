<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
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

        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-amber-900 mb-2">Đăng nhập</h2>
            <p class="text-sm text-gray-500 mb-6">Chào mừng bạn trở lại!</p>

            <div v-if="status" class="mb-4 text-sm font-medium text-green-600 bg-green-50 p-3 rounded-lg">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        autofocus
                        autocomplete="username"
                        class="w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 py-3"
                        placeholder="email@example.com"
                    />
                    <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu</label>
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 py-3"
                        placeholder="••••••••"
                    />
                    <p v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</p>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input v-model="form.remember" type="checkbox" class="rounded text-amber-600 focus:ring-amber-500" />
                        <span class="ml-2 text-sm text-gray-600">Ghi nhớ đăng nhập</span>
                    </label>
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-amber-700 hover:text-amber-900 font-medium"
                    >
                        Quên mật khẩu?
                    </Link>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full py-3 bg-amber-700 text-white rounded-xl font-semibold hover:bg-amber-600 transition disabled:opacity-50"
                >
                    {{ form.processing ? 'Đang đăng nhập...' : 'Đăng nhập' }}
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Chưa có tài khoản?
                <Link :href="route('register')" class="text-amber-700 hover:text-amber-900 font-semibold">Đăng ký ngay</Link>
            </p>
        </div>
    </GuestLayout>
</template>
