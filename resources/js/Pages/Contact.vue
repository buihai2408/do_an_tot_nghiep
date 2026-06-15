<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const form = ref({ name: '', email: '', phone: '', message: '' });
const submitted = ref(false);
const submit = () => { submitted.value = true; };
</script>

<template>
    <AppLayout>
        <!-- Hero -->
        <section class="py-16 lg:py-20" style="background:#2C1810;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-xs font-semibold tracking-[0.3em] uppercase mb-3 flex items-center justify-center gap-3" style="color:#D4A853;">
                    <span class="w-6 h-px" style="background:#D4A853;"></span>
                    Liên Hệ
                    <span class="w-6 h-px" style="background:#D4A853;"></span>
                </p>
                <h1 class="text-4xl lg:text-5xl font-bold text-white" style="font-family:'Playfair Display',serif;">Ghé Thăm Chúng Tôi</h1>
                <p class="mt-3" style="color:rgba(255,255,255,0.6);">Chúng tôi luôn sẵn sàng lắng nghe bạn</p>
            </div>
        </section>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">

                <!-- Contact Info -->
                <div>
                    <p class="text-xs font-semibold tracking-[0.3em] uppercase mb-4" style="color:#D4A853;">Thông tin liên hệ</p>
                    <h2 class="text-3xl font-bold mb-10" style="color:#2C1810; font-family:'Playfair Display',serif;">Tìm Chúng Tôi</h2>

                    <div class="space-y-7">
                        <!-- Address -->
                        <div class="flex gap-5 group">
                            <div class="w-12 h-12 flex items-center justify-center flex-shrink-0 transition group-hover:scale-110"
                                style="background:#2C1810; border-radius:8px; box-shadow:0 4px 15px rgba(44,24,16,0.2);">
                                <svg class="w-5 h-5" style="color:#D4A853;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold tracking-widest uppercase mb-1" style="color:#2C1810;">Địa chỉ</h3>
                                <p class="text-sm leading-relaxed" style="color:#8B7355;">175 Tây Sơn<br>Đống Đa, Hà Nội</p>
                            </div>
                        </div>
                        <!-- Phone -->
                        <div class="flex gap-5 group">
                            <div class="w-12 h-12 flex items-center justify-center flex-shrink-0 transition group-hover:scale-110"
                                style="background:#2C1810; border-radius:8px; box-shadow:0 4px 15px rgba(44,24,16,0.2);">
                                <svg class="w-5 h-5" style="color:#D4A853;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold tracking-widest uppercase mb-1" style="color:#2C1810;">Điện thoại</h3>
                                <p class="text-sm" style="color:#8B7355;">0966461728</p>
                            </div>
                        </div>
                        <!-- Email -->
                        <div class="flex gap-5 group">
                            <div class="w-12 h-12 flex items-center justify-center flex-shrink-0 transition group-hover:scale-110"
                                style="background:#2C1810; border-radius:8px; box-shadow:0 4px 15px rgba(44,24,16,0.2);">
                                <svg class="w-5 h-5" style="color:#D4A853;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold tracking-widest uppercase mb-1" style="color:#2C1810;">Email</h3>
                                <p class="text-sm" style="color:#8B7355;">info@coffeeshop.vn</p>
                            </div>
                        </div>
                        <!-- Hours -->
                        <div class="flex gap-5 group">
                            <div class="w-12 h-12 flex items-center justify-center flex-shrink-0 transition group-hover:scale-110"
                                style="background:#2C1810; border-radius:8px; box-shadow:0 4px 15px rgba(44,24,16,0.2);">
                                <svg class="w-5 h-5" style="color:#D4A853;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold tracking-widest uppercase mb-1" style="color:#2C1810;">Giờ mở cửa</h3>
                                <p class="text-sm" style="color:#8B7355;">Thứ 2 - Thứ 6: 07:00 - 22:00</p>
                                <p class="text-sm" style="color:#8B7355;">Thứ 7 - CN: 08:00 - 23:00</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div>
                    <div v-if="submitted" class="p-10 text-center" style="background:#FAF6F0; border:1px solid #E8D9C5; border-radius:4px;">
                        <div class="w-16 h-16 mx-auto mb-6 rounded-full flex items-center justify-center" style="background:#ECFDF5;">
                            <svg class="w-8 h-8" style="color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2" style="color:#2C1810; font-family:'Playfair Display',serif;">Cảm ơn bạn! ☕</h3>
                        <p class="text-sm mb-6" style="color:#8B7355;">Chúng tôi đã nhận được tin nhắn và sẽ phản hồi sớm nhất có thể.</p>
                        <button @click="submitted = false; form = { name: '', email: '', phone: '', message: '' }"
                            class="text-sm font-semibold transition hover:opacity-70"
                            style="color:#D4A853; border-bottom:1px solid #D4A853; padding-bottom:2px;">
                            Gửi tin nhắn khác
                        </button>
                    </div>

                    <form v-else @submit.prevent="submit" class="p-8 lg:p-10" style="background:#FAF6F0; border:1px solid #E8D9C5; border-radius:4px;">
                        <h3 class="text-xs font-semibold tracking-[0.2em] uppercase mb-6" style="color:#2C1810;">Gửi tin nhắn cho chúng tôi</h3>
                        <div class="space-y-5">
                            <div>
                                <label class="block text-xs font-semibold tracking-widest uppercase mb-2" style="color:#8B7355;">Họ tên *</label>
                                <input v-model="form.name" type="text" required
                                    class="w-full border py-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-offset-0"
                                    style="border-color:#E8D9C5; border-radius:4px; color:#2C1810;"
                                    placeholder="Nguyễn Văn A"
                                    onfocus="this.style.borderColor='#D4A853'; this.style.boxShadow='0 0 0 3px rgba(212,168,83,0.15)'"
                                    onblur="this.style.borderColor='#E8D9C5'; this.style.boxShadow='none'" />
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold tracking-widest uppercase mb-2" style="color:#8B7355;">Email</label>
                                    <input v-model="form.email" type="email"
                                        class="w-full border py-3 text-sm bg-white"
                                        style="border-color:#E8D9C5; border-radius:4px; color:#2C1810;"
                                        placeholder="email@example.com"
                                        onfocus="this.style.borderColor='#D4A853'; this.style.boxShadow='0 0 0 3px rgba(212,168,83,0.15)'"
                                        onblur="this.style.borderColor='#E8D9C5'; this.style.boxShadow='none'" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold tracking-widests uppercase mb-2" style="color:#8B7355;">Số điện thoại</label>
                                    <input v-model="form.phone" type="tel"
                                        class="w-full border py-3 text-sm bg-white"
                                        style="border-color:#E8D9C5; border-radius:4px; color:#2C1810;"
                                        placeholder="0966461728"
                                        onfocus="this.style.borderColor='#D4A853'; this.style.boxShadow='0 0 0 3px rgba(212,168,83,0.15)'"
                                        onblur="this.style.borderColor='#E8D9C5'; this.style.boxShadow='none'" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold tracking-widests uppercase mb-2" style="color:#8B7355;">Tin nhắn *</label>
                                <textarea v-model="form.message" rows="5" required
                                    class="w-full border text-sm bg-white"
                                    style="border-color:#E8D9C5; border-radius:4px; color:#2C1810; padding:12px 14px;"
                                    placeholder="Nội dung tin nhắn..."
                                    onfocus="this.style.borderColor='#D4A853'; this.style.boxShadow='0 0 0 3px rgba(212,168,83,0.15)'"
                                    onblur="this.style.borderColor='#E8D9C5'; this.style.boxShadow='none'"></textarea>
                            </div>
                            <button type="submit"
                                class="w-full py-3.5 text-sm font-semibold tracking-wider uppercase transition-all duration-200 hover:-translate-y-0.5"
                                style="background:#2C1810; color:white; border-radius:4px; box-shadow:0 4px 15px rgba(44,24,16,0.25);">
                                ✉️ Gửi tin nhắn
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Map placeholder -->
        <section class="py-12" style="background:#FAF6F0;">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-center" style="background:#E8D9C5; height:250px; border-radius:4px; border:1px solid #D4B896;">
                    <div class="text-center">
                        <div class="text-4xl mb-3">📍</div>
                        <p class="text-sm font-semibold" style="color:#2C1810;">175 Tây Sơn, Đống Đa, Hà Nội</p>
                        <p class="text-xs mt-1" style="color:#8B7355;">Google Maps sẽ được tích hợp tại đây</p>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
