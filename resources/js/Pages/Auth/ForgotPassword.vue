<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Quên mật khẩu" />

        <div class="bg-white p-8 lg:p-10 border border-[#E8D9C5] rounded-lg shadow-[0_4px_20px_rgba(44,24,16,0.08)]">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-[#2C1810] font-playfair mb-1">Quên mật khẩu?</h2>
                <p class="text-sm text-[#8B7355] leading-relaxed">
                    Nhập địa chỉ email của bạn và chúng tôi sẽ gửi link đặt lại mật khẩu mới.
                </p>
            </div>

            <div v-if="status" class="mb-5 text-sm font-medium text-[#065F46] bg-[#ECFDF5] border border-[#A7F3D0] rounded-md p-3">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold tracking-widest uppercase text-[#8B7355] mb-2">Địa chỉ Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        autofocus
                        autocomplete="username"
                        class="w-full text-sm py-3 text-[#2C1810] border-[#E8D9C5] rounded focus:border-[#D4A853] focus:ring focus:ring-[#D4A853]/20 transition"
                        placeholder="email@example.com"
                    />
                    <p v-if="form.errors.email" class="text-red-500 text-xs mt-1.5">{{ form.errors.email }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full py-3.5 bg-[#2C1810] text-white text-sm font-semibold tracking-wider uppercase rounded hover:bg-[#5C3A1E] transition disabled:opacity-50 shadow-[0_4px_15px_rgba(44,24,16,0.25)] hover:shadow-none"
                >
                    {{ form.processing ? '⏳ Đang gửi...' : 'Gửi link đặt lại mật khẩu' }}
                </button>
            </form>
        </div>
    </GuestLayout>
</template>
