<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<template>
    <AppLayout>
        <!-- Hero -->
        <section class="relative min-h-[50vh] flex items-center justify-center bg-cover bg-center" style="background-image: url('/images/background_home.jpg')">
            <div class="absolute inset-0 bg-black/60"></div>
            <div class="relative z-10 text-center px-4">
                <h1 class="text-4xl lg:text-6xl font-bold text-white tracking-wide" style="font-family: 'Playfair Display', serif;">Câu Chuyện Của Chúng Tôi</h1>
                <p class="text-gray-300 mt-4 max-w-xl mx-auto">Hành trình mang đến những ly cà phê tuyệt vời nhất</p>
            </div>
        </section>

        <!-- Story -->
        <section class="py-20 lg:py-28">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <p class="text-xs font-semibold tracking-widest uppercase text-amber-600 mb-4">Về chúng tôi</p>
                        <h2 class="text-3xl lg:text-4xl font-bold text-[#1a1a1a] leading-tight mb-6" style="font-family: 'Playfair Display', serif;">
                            Từ Đam Mê Đến<br>Tách Cà Phê Hoàn Hảo
                        </h2>
                        <div class="space-y-4 text-gray-600 leading-relaxed">
                            <p>
                                The Coffee Shop được thành lập với một ước mơ đơn giản: mang đến cho mọi người những tách cà phê ngon nhất, được pha chế từ những hạt cà phê chọn lọc kỹ lưỡng từ các vùng trồng nổi tiếng của Việt Nam.
                            </p>
                            <p>
                                Chúng tôi tin rằng một ly cà phê ngon không chỉ đến từ nguyên liệu tốt, mà còn từ sự tỉ mỉ trong từng công đoạn pha chế, từ tâm huyết của người barista và từ không gian mà bạn thưởng thức nó.
                            </p>
                            <p>
                                Mỗi ngày, đội ngũ của chúng tôi làm việc với niềm đam mê để đảm bảo rằng từng giọt cà phê đều mang lại trải nghiệm tuyệt vời nhất cho khách hàng.
                            </p>
                        </div>
                    </div>
                    <div class="aspect-[4/5] bg-[#f8f5f0] overflow-hidden">
                        <img src="/images/background_home.jpg" class="w-full h-full object-cover" alt="Coffee shop" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Values -->
        <section class="py-20 lg:py-28 bg-[#f8f5f0]">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <p class="text-xs font-semibold tracking-widest uppercase text-amber-600 mb-3">Giá trị cốt lõi</p>
                    <h2 class="text-3xl lg:text-4xl font-bold text-[#1a1a1a]" style="font-family: 'Playfair Display', serif;">Điều Chúng Tôi Theo Đuổi</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-6 bg-[#1a1a1a] flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                        <h3 class="text-sm font-bold tracking-widest uppercase text-[#1a1a1a] mb-3">Đam Mê</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Mỗi tách cà phê là một tác phẩm nghệ thuật, được tạo ra từ sự đam mê và tận tâm của đội ngũ chúng tôi.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-6 bg-[#1a1a1a] flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h3 class="text-sm font-bold tracking-widest uppercase text-[#1a1a1a] mb-3">Chất Lượng</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Nguyên liệu được chọn lọc từ những vùng trồng tốt nhất, đảm bảo hương vị thuần khiết trong từng giọt.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-6 bg-[#1a1a1a] flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <h3 class="text-sm font-bold tracking-widest uppercase text-[#1a1a1a] mb-3">Cộng Đồng</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Chúng tôi xây dựng không gian nơi mọi người có thể kết nối, chia sẻ và tận hưởng những khoảnh khắc đáng nhớ.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Numbers -->
        <section class="py-20 lg:py-24 bg-[#1a1a1a]">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div>
                        <p class="text-4xl lg:text-5xl font-bold text-white" style="font-family: 'Playfair Display', serif;">5+</p>
                        <p class="text-xs text-gray-400 tracking-widest uppercase mt-2">Năm kinh nghiệm</p>
                    </div>
                    <div>
                        <p class="text-4xl lg:text-5xl font-bold text-white" style="font-family: 'Playfair Display', serif;">50+</p>
                        <p class="text-xs text-gray-400 tracking-widest uppercase mt-2">Loại đồ uống</p>
                    </div>
                    <div>
                        <p class="text-4xl lg:text-5xl font-bold text-white" style="font-family: 'Playfair Display', serif;">10K+</p>
                        <p class="text-xs text-gray-400 tracking-widest uppercase mt-2">Khách hàng</p>
                    </div>
                    <div>
                        <p class="text-4xl lg:text-5xl font-bold text-white" style="font-family: 'Playfair Display', serif;">100%</p>
                        <p class="text-xs text-gray-400 tracking-widest uppercase mt-2">Hài lòng</p>
                    </div>
                </div>
            </div>
        </section>
    </AppLayout>
</template>
