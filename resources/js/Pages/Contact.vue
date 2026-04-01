<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const form = ref({
    name: '',
    email: '',
    phone: '',
    message: '',
});
const submitted = ref(false);

const submit = () => {
    submitted.value = true;
};
</script>

<template>
    <AppLayout>
        <!-- Hero -->
        <section class="bg-[#1a1a1a] py-16 lg:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl lg:text-5xl font-bold text-white" style="font-family: 'Playfair Display', serif;">Liên Hệ</h1>
                <p class="text-gray-400 mt-3">Chúng tôi luôn sẵn sàng lắng nghe bạn</p>
            </div>
        </section>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                <!-- Contact Info -->
                <div>
                    <p class="text-xs font-semibold tracking-widest uppercase text-amber-600 mb-4">Thông tin liên hệ</p>
                    <h2 class="text-3xl font-bold text-[#1a1a1a] mb-8" style="font-family: 'Playfair Display', serif;">Ghé Thăm Chúng Tôi</h2>

                    <div class="space-y-8">
                        <div class="flex gap-4">
                            <div class="w-12 h-12 bg-[#1a1a1a] flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold tracking-widest uppercase text-[#1a1a1a] mb-1">Địa chỉ</h3>
                                <p class="text-sm text-gray-500 leading-relaxed">Số 99, Đường Cà Phê<br>Quận 1, TP. Hồ Chí Minh</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-12 h-12 bg-[#1a1a1a] flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold tracking-widest uppercase text-[#1a1a1a] mb-1">Điện thoại</h3>
                                <p class="text-sm text-gray-500">0901 234 567</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-12 h-12 bg-[#1a1a1a] flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold tracking-widest uppercase text-[#1a1a1a] mb-1">Email</h3>
                                <p class="text-sm text-gray-500">info@coffeeshop.vn</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-12 h-12 bg-[#1a1a1a] flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold tracking-widest uppercase text-[#1a1a1a] mb-1">Giờ mở cửa</h3>
                                <p class="text-sm text-gray-500">Thứ 2 - Thứ 6: 07:00 - 22:00</p>
                                <p class="text-sm text-gray-500">Thứ 7 - CN: 08:00 - 23:00</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div>
                    <div v-if="submitted" class="bg-[#f8f5f0] p-10 text-center">
                        <div class="w-16 h-16 mx-auto mb-6 bg-green-50 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#1a1a1a] mb-2" style="font-family: 'Playfair Display', serif;">Cảm ơn bạn!</h3>
                        <p class="text-sm text-gray-500 mb-6">Chúng tôi đã nhận được tin nhắn và sẽ phản hồi sớm nhất có thể.</p>
                        <button @click="submitted = false; form = { name: '', email: '', phone: '', message: '' }" class="text-sm text-[#1a1a1a] font-semibold border-b border-[#1a1a1a] pb-0.5 hover:text-amber-700 hover:border-amber-700 transition">
                            Gửi tin nhắn khác
                        </button>
                    </div>

                    <form v-else @submit.prevent="submit" class="bg-[#f8f5f0] p-8 lg:p-10">
                        <h3 class="text-xs font-semibold tracking-widest uppercase text-[#1a1a1a] mb-6">Gửi tin nhắn cho chúng tôi</h3>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Họ tên *</label>
                                <input v-model="form.name" type="text" required class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] py-3 text-sm bg-white" placeholder="Nguyễn Văn A" />
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Email</label>
                                    <input v-model="form.email" type="email" class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] py-3 text-sm bg-white" placeholder="email@example.com" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Số điện thoại</label>
                                    <input v-model="form.phone" type="tel" class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] py-3 text-sm bg-white" placeholder="0901234567" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Tin nhắn *</label>
                                <textarea v-model="form.message" rows="5" required class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] text-sm bg-white" placeholder="Nội dung tin nhắn..."></textarea>
                            </div>
                            <button type="submit" class="w-full bg-[#1a1a1a] text-white py-3.5 text-sm font-semibold tracking-wider uppercase hover:bg-[#333] transition">
                                Gửi tin nhắn
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Map placeholder -->
        <section class="bg-[#f8f5f0] py-16">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="aspect-[21/9] bg-gray-200 flex items-center justify-center">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <p class="text-sm text-gray-500">Số 99, Đường Cà Phê, Quận 1, TP.HCM</p>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
