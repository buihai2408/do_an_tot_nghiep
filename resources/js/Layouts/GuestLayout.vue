<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex" style="font-family: 'Inter', sans-serif;">
        <!-- Left side - Branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-[#1a1a1a] relative overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('/images/background_home.jpg')"></div>
            <div class="relative z-10 flex flex-col justify-center px-16">
                <Link href="/" class="block mb-8">
                    <span class="text-4xl font-bold text-white tracking-wider" style="font-family: 'Playfair Display', serif;">The Coffee Shop</span>
                </Link>
                <p class="text-lg text-gray-400 leading-relaxed max-w-md">
                    Thưởng thức hương vị cà phê đích thực, pha chế từ những hạt cà phê được chọn lọc kỹ lưỡng.
                </p>
                <div class="mt-12 flex items-center gap-4">
                    <div class="w-12 h-px bg-gray-600"></div>
                    <p class="text-xs text-gray-500 tracking-widest uppercase">Great Coffee, Every Day</p>
                </div>
            </div>
        </div>

        <!-- Right side - Form -->
        <div class="w-full lg:w-1/2 flex flex-col items-center justify-center bg-[#f8f5f0] px-6 py-12">
            <div class="lg:hidden mb-8">
                <Link href="/" class="block">
                    <span class="text-2xl font-bold text-[#1a1a1a] tracking-wider" style="font-family: 'Playfair Display', serif;">The Coffee Shop</span>
                </Link>
            </div>
            <div class="w-full max-w-md">
                <slot />
            </div>
        </div>
    </div>
</template>
