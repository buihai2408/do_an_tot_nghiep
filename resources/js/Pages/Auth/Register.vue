<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const form = useForm({
    name: '',
    phone: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const clientErrors = ref({ phone: '', email: '' });

const phoneRegex = /^(0[35789])\d{8}$/;
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

watch(() => form.phone, (val) => {
    if (!val) { clientErrors.value.phone = ''; return; }
    clientErrors.value.phone = phoneRegex.test(val) ? '' : 'Số điện thoại không đúng định dạng (VD: 0901234567).';
});

watch(() => form.email, (val) => {
    if (!val) { clientErrors.value.email = ''; return; }
    clientErrors.value.email = emailRegex.test(val) ? '' : 'Email không đúng định dạng (VD: email@example.com).';
});

const submit = () => {
    if (clientErrors.value.phone || clientErrors.value.email) return;
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Đăng ký" />

        <div class="bg-white p-8 lg:p-10 shadow-sm">
            <h2 class="text-2xl font-bold text-[#1a1a1a] mb-1" style="font-family: 'Playfair Display', serif;">Tạo tài khoản</h2>
            <p class="text-sm text-gray-400 mb-8">Đăng ký để đặt hàng và nhận ưu đãi.</p>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Họ tên *</label>
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        autofocus
                        autocomplete="name"
                        class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] py-3 text-sm"
                        placeholder="Nguyễn Văn A"
                    />
                    <p v-if="form.errors.name" class="text-red-500 text-xs mt-1.5">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Số điện thoại *</label>
                    <input
                        v-model="form.phone"
                        type="tel"
                        required
                        autocomplete="tel"
                        :class="(clientErrors.phone || form.errors.phone) ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a]'"
                        class="w-full py-3 text-sm"
                        placeholder="0901234567"
                    />
                    <p v-if="clientErrors.phone" class="text-red-500 text-xs mt-1.5">{{ clientErrors.phone }}</p>
                    <p v-else-if="form.errors.phone" class="text-red-500 text-xs mt-1.5">{{ form.errors.phone }}</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Email <span class="text-gray-400 font-normal normal-case tracking-normal">(không bắt buộc)</span></label>
                    <input
                        v-model="form.email"
                        type="email"
                        autocomplete="username"
                        :class="(clientErrors.email || form.errors.email) ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a]'"
                        class="w-full py-3 text-sm"
                        placeholder="email@example.com"
                    />
                    <p v-if="clientErrors.email" class="text-red-500 text-xs mt-1.5">{{ clientErrors.email }}</p>
                    <p v-else-if="form.errors.email" class="text-red-500 text-xs mt-1.5">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Mật khẩu *</label>
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] py-3 text-sm"
                        placeholder="Tối thiểu 8 ký tự"
                    />
                    <p v-if="form.errors.password" class="text-red-500 text-xs mt-1.5">{{ form.errors.password }}</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Xác nhận mật khẩu *</label>
                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] py-3 text-sm"
                        placeholder="Nhập lại mật khẩu"
                    />
                    <p v-if="form.errors.password_confirmation" class="text-red-500 text-xs mt-1.5">{{ form.errors.password_confirmation }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full py-3.5 bg-[#1a1a1a] text-white text-sm font-semibold tracking-wider uppercase hover:bg-[#333] transition disabled:opacity-50"
                >
                    {{ form.processing ? 'Đang đăng ký...' : 'Đăng ký' }}
                </button>
            </form>

            <p class="text-center text-sm text-gray-400 mt-8">
                Đã có tài khoản?
                <Link :href="route('login')" class="text-[#1a1a1a] font-semibold hover:underline">Đăng nhập</Link>
            </p>
        </div>
    </GuestLayout>
</template>
