<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Đặt lại mật khẩu" />

        <div class="bg-white p-8 lg:p-10 border border-[#E8D9C5] rounded-lg shadow-[0_4px_20px_rgba(44,24,16,0.08)]">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-[#2C1810] font-playfair mb-1">Đặt lại mật khẩu</h2>
                <p class="text-sm text-[#8B7355]">Nhập mật khẩu mới cho tài khoản của bạn.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold tracking-widest uppercase text-[#8B7355] mb-2">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        autofocus
                        autocomplete="username"
                        class="w-full text-sm py-3 text-[#2C1810] border-[#E8D9C5] rounded focus:border-[#D4A853] focus:ring focus:ring-[#D4A853]/20 transition"
                    />
                    <p v-if="form.errors.email" class="text-red-500 text-xs mt-1.5">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold tracking-widest uppercase text-[#8B7355] mb-2">Mật khẩu mới</label>
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="w-full text-sm py-3 text-[#2C1810] border-[#E8D9C5] rounded focus:border-[#D4A853] focus:ring focus:ring-[#D4A853]/20 transition"
                        placeholder="Tối thiểu 8 ký tự"
                    />
                    <p v-if="form.errors.password" class="text-red-500 text-xs mt-1.5">{{ form.errors.password }}</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold tracking-widest uppercase text-[#8B7355] mb-2">Xác nhận mật khẩu</label>
                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="w-full text-sm py-3 text-[#2C1810] border-[#E8D9C5] rounded focus:border-[#D4A853] focus:ring focus:ring-[#D4A853]/20 transition"
                        placeholder="Nhập lại mật khẩu mới"
                    />
                    <p v-if="form.errors.password_confirmation" class="text-red-500 text-xs mt-1.5">{{ form.errors.password_confirmation }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full py-3.5 bg-[#2C1810] text-white text-sm font-semibold tracking-wider uppercase rounded hover:bg-[#5C3A1E] transition disabled:opacity-50 shadow-[0_4px_15px_rgba(44,24,16,0.25)] hover:shadow-none"
                >
                    {{ form.processing ? '⏳ Đang đặt lại...' : 'Đặt lại mật khẩu' }}
                </button>
            </form>
        </div>
    </GuestLayout>
</template>
