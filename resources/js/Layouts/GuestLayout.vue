<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex">
        <!-- Left side - Branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-amber-900 via-amber-800 to-amber-700 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-20 left-20 text-[200px]">☕</div>
                <div class="absolute bottom-20 right-20 text-[150px]">☕</div>
            </div>
            <div class="relative z-10 flex flex-col justify-center px-16">
                <Link href="/" class="flex items-center space-x-3 mb-8">
                    <span class="text-5xl">☕</span>
                    <span class="text-4xl font-bold text-amber-100">Coffee Shop</span>
                </Link>
                <p class="text-xl text-amber-200 leading-relaxed max-w-md">
                    Thưởng thức hương vị cà phê đích thực, pha chế từ những hạt cà phê được chọn lọc kỹ lưỡng.
                </p>
            </div>
        </div>

        <!-- Right side - Form -->
        <div class="w-full lg:w-1/2 flex flex-col items-center justify-center bg-amber-50 px-6 py-12">
            <div class="lg:hidden mb-8">
                <Link href="/" class="flex items-center space-x-2">
                    <span class="text-3xl">☕</span>
                    <span class="text-2xl font-bold text-amber-900">Coffee Shop</span>
                </Link>
            </div>
            <div class="w-full max-w-md">
                <slot />
            </div>
        </div>
    </div>
</template>
