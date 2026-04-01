<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const mobileMenuOpen = ref(false);
const userDropdownOpen = ref(false);
let dropdownTimeout = null;

function openDropdown() {
    clearTimeout(dropdownTimeout);
    userDropdownOpen.value = true;
}

function closeDropdown() {
    dropdownTimeout = setTimeout(() => {
        userDropdownOpen.value = false;
    }, 150);
}

const navigation = [
    { name: 'Trang chủ', href: '/' },
    { name: 'Menu', href: '/menu' },
    { name: 'Giới thiệu', href: '/about' },
    { name: 'Liên hệ', href: '/contact' },
];
</script>

<template>
    <div class="min-h-screen bg-white" style="font-family: 'Inter', sans-serif;">
        <nav class="bg-[#1a1a1a] sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16 lg:h-20">
                    <Link href="/" class="flex items-center space-x-2">
                        <span class="text-2xl font-bold text-white tracking-wider" style="font-family: 'Playfair Display', serif;">The Coffee Shop</span>
                    </Link>

                    <div class="hidden md:flex items-center space-x-8">
                        <Link
                            v-for="item in navigation"
                            :key="item.name"
                            :href="item.href"
                            class="text-sm font-medium text-gray-300 hover:text-white tracking-widest uppercase transition"
                        >
                            {{ item.name }}
                        </Link>
                    </div>

                    <div class="flex items-center space-x-5">
                        <Link href="/cart" class="relative text-gray-300 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                            </svg>
                            <span v-if="page.props.cart_count > 0" class="absolute -top-2 -right-2 bg-white text-[#1a1a1a] text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center">
                                {{ page.props.cart_count }}
                            </span>
                        </Link>

                        <template v-if="page.props.auth.user">
                            <div class="relative" @mouseenter="openDropdown" @mouseleave="closeDropdown">
                                <button class="flex items-center text-sm text-gray-300 hover:text-white transition">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    {{ page.props.auth.user.name }}
                                </button>
                                <div v-show="userDropdownOpen" class="absolute right-0 top-full w-52 pt-2 z-50">
                                    <div class="bg-white shadow-xl border border-gray-100 py-2">
                                        <Link href="/orders" class="block px-5 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">Đơn hàng của tôi</Link>
                                        <Link href="/loyalty" class="flex items-center justify-between px-5 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                            <span>Điểm thưởng</span>
                                            <span v-if="page.props.auth.loyalty_points > 0" class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">
                                                {{ page.props.auth.loyalty_tier_icon }} {{ page.props.auth.loyalty_points }}
                                            </span>
                                        </Link>
                                        <Link href="/profile" class="block px-5 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">Hồ sơ</Link>
                                        <Link v-if="page.props.auth.user.role === 'admin' || page.props.auth.user.role === 'staff'" href="/admin" class="block px-5 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">Quản trị</Link>
                                        <hr class="my-1">
                                        <Link href="/logout" method="post" as="button" class="block w-full text-left px-5 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">Đăng xuất</Link>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <Link href="/login" class="hidden md:inline text-sm text-gray-300 hover:text-white tracking-wider uppercase transition">Đăng nhập</Link>
                            <Link href="/register" class="hidden md:inline text-sm bg-white text-[#1a1a1a] px-4 py-2 font-semibold tracking-wider uppercase hover:bg-gray-200 transition">Đăng ký</Link>
                        </template>

                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-300 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="mobileMenuOpen" class="md:hidden bg-[#1a1a1a] border-t border-gray-800 pb-4">
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    class="block px-6 py-3 text-sm text-gray-300 hover:text-white tracking-widest uppercase"
                    @click="mobileMenuOpen = false"
                >
                    {{ item.name }}
                </Link>
                <template v-if="!page.props.auth.user">
                    <Link href="/login" class="block px-6 py-3 text-sm text-gray-300 hover:text-white tracking-widest uppercase" @click="mobileMenuOpen = false">Đăng nhập</Link>
                    <Link href="/register" class="block px-6 py-3 text-sm text-gray-300 hover:text-white tracking-widest uppercase" @click="mobileMenuOpen = false">Đăng ký</Link>
                </template>
            </div>
        </nav>

        <main>
            <slot />
        </main>

        <footer class="bg-[#1a1a1a] text-gray-400">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                    <div class="md:col-span-1">
                        <h3 class="text-xl font-bold text-white mb-4" style="font-family: 'Playfair Display', serif;">The Coffee Shop</h3>
                        <p class="text-sm leading-relaxed">Thưởng thức hương vị cà phê đích thực, pha chế từ những hạt cà phê được chọn lọc kỹ lưỡng.</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-white tracking-widest uppercase mb-5">Khám phá</h4>
                        <ul class="space-y-3 text-sm">
                            <li><Link href="/menu" class="hover:text-white transition">Menu</Link></li>
                            <li><Link href="/about" class="hover:text-white transition">Giới thiệu</Link></li>
                            <li><Link href="/contact" class="hover:text-white transition">Liên hệ</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-white tracking-widest uppercase mb-5">Giờ mở cửa</h4>
                        <ul class="space-y-3 text-sm">
                            <li>Thứ 2 - Thứ 6: 07:00 - 22:00</li>
                            <li>Thứ 7 - CN: 08:00 - 23:00</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-white tracking-widest uppercase mb-5">Liên hệ</h4>
                        <ul class="space-y-3 text-sm">
                            <li>Số 99, Đường Cà Phê, Quận 1, TP.HCM</li>
                            <li>0901 234 567</li>
                            <li>info@coffeeshop.vn</li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center text-xs text-gray-500">
                    <p>&copy; {{ new Date().getFullYear() }} The Coffee Shop. All rights reserved.</p>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <a href="#" class="hover:text-white transition">Chính sách bảo mật</a>
                        <a href="#" class="hover:text-white transition">Điều khoản sử dụng</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
