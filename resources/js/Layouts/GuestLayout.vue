<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex" style="font-family:'Inter',sans-serif;">
        <!-- Left side - Branding (desktop only) -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden" style="background:#2C1810;">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image:url('/images/background_home.jpg'); opacity:0.25;"></div>
            <!-- Warm overlay gradient -->
            <div class="absolute inset-0" style="background:linear-gradient(135deg, rgba(44,24,16,0.7) 0%, rgba(92,58,30,0.4) 100%);"></div>

            <!-- Decorative coffee ring top-right -->
            <div class="absolute top-10 right-10 w-32 h-32 rounded-full border-2 opacity-10" style="border-color:#D4A853;"></div>
            <div class="absolute top-14 right-14 w-24 h-24 rounded-full border opacity-10" style="border-color:#D4A853;"></div>

            <div class="relative z-10 flex flex-col justify-center px-16">
                <Link href="/" class="block mb-8">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background:#D4A853;">
                            <svg class="w-6 h-6" style="color:#2C1810;" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/>
                            </svg>
                        </div>
                        <span class="text-3xl font-bold text-white" style="font-family:'Playfair Display',serif;">The Coffee Shop</span>
                    </div>
                </Link>

                <!-- Quote -->
                <div class="relative">
                    <span class="absolute -top-4 -left-2 text-6xl leading-none opacity-20" style="color:#D4A853; font-family:Georgia,serif;">"</span>
                    <p class="text-lg leading-relaxed pl-4" style="color:rgba(255,255,255,0.75);">
                        Thưởng thức hương vị cà phê đích thực, pha chế từ những hạt cà phê được chọn lọc kỹ lưỡng.
                    </p>
                </div>

                <div class="mt-10 flex items-center gap-4">
                    <div class="h-px w-12" style="background:#D4A853;"></div>
                    <p class="text-xs tracking-widest uppercase" style="color:#D4A853;">Great Coffee, Every Day</p>
                </div>

                <!-- Stats -->
                <div class="mt-12 grid grid-cols-3 gap-6">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-white" style="font-family:'Playfair Display',serif;">10K+</p>
                        <p class="text-xs mt-1" style="color:rgba(255,255,255,0.5);">Khách hàng</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-white" style="font-family:'Playfair Display',serif;">50+</p>
                        <p class="text-xs mt-1" style="color:rgba(255,255,255,0.5);">Đồ uống</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-white" style="font-family:'Playfair Display',serif;">5★</p>
                        <p class="text-xs mt-1" style="color:rgba(255,255,255,0.5);">Đánh giá</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right side - Form -->
        <div class="w-full lg:w-1/2 flex flex-col items-center justify-center px-6 py-12" style="background:#FAF6F0;">
            <!-- Mobile logo -->
            <div class="lg:hidden mb-8">
                <Link href="/" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#2C1810;">
                        <svg class="w-5 h-5" style="color:#D4A853;" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold" style="color:#2C1810; font-family:'Playfair Display',serif;">The Coffee Shop</span>
                </Link>
            </div>
            <div class="w-full max-w-md">
                <slot />
            </div>
        </div>
    </div>
</template>
