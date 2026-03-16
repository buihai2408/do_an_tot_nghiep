<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    phone: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Đăng ký" />

        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-amber-900 mb-2">Tạo tài khoản</h2>
            <p class="text-sm text-gray-500 mb-6">Đăng ký để đặt hàng và nhận ưu đãi.</p>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên *</label>
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        autofocus
                        autocomplete="name"
                        class="w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 py-3"
                        placeholder="Nguyễn Văn A"
                    />
                    <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại *</label>
                    <input
                        v-model="form.phone"
                        type="tel"
                        required
                        autocomplete="tel"
                        class="w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 py-3"
                        placeholder="0901 234 567"
                    />
                    <p v-if="form.errors.phone" class="text-red-500 text-sm mt-1">{{ form.errors.phone }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        autocomplete="username"
                        class="w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 py-3"
                        placeholder="email@example.com"
                    />
                    <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu *</label>
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 py-3"
                        placeholder="Tối thiểu 8 ký tự"
                    />
                    <p v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu *</label>
                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 py-3"
                        placeholder="Nhập lại mật khẩu"
                    />
                    <p v-if="form.errors.password_confirmation" class="text-red-500 text-sm mt-1">{{ form.errors.password_confirmation }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full py-3 bg-amber-700 text-white rounded-xl font-semibold hover:bg-amber-600 transition disabled:opacity-50"
                >
                    {{ form.processing ? 'Đang đăng ký...' : 'Đăng ký' }}
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Đã có tài khoản?
                <Link :href="route('login')" class="text-amber-700 hover:text-amber-900 font-semibold">Đăng nhập</Link>
            </p>
        </div>
    </GuestLayout>
</template>
