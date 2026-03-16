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
    { name: 'Trang chủ', href: '/', routeName: 'home' },
    { name: 'Thực đơn', href: '/menu', routeName: 'menu.index' },
];
</script>

<template>
    <div class="min-h-screen bg-amber-50">
        <!-- Navbar -->
        <nav class="bg-amber-900 shadow-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <Link href="/" class="flex items-center space-x-2">
                            <span class="text-2xl">☕</span>
                            <span class="text-xl font-bold text-amber-100">Coffee Shop</span>
                        </Link>
                        <div class="hidden sm:flex sm:ml-8 sm:space-x-4">
                            <Link
                                v-for="item in navigation"
                                :key="item.name"
                                :href="item.href"
                                class="px-3 py-2 rounded-md text-sm font-medium text-amber-200 hover:text-white hover:bg-amber-800 transition"
                            >
                                {{ item.name }}
                            </Link>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <Link href="/cart" class="relative text-amber-200 hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                            </svg>
                            <span v-if="page.props.cart_count > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ page.props.cart_count }}
                            </span>
                        </Link>

                        <template v-if="page.props.auth.user">
                            <div class="relative" @mouseenter="openDropdown" @mouseleave="closeDropdown">
                                <button class="flex items-center text-sm text-amber-200 hover:text-white transition">
                                    {{ page.props.auth.user.name }}
                                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div
                                    v-show="userDropdownOpen"
                                    class="absolute right-0 top-full w-48 pt-2 z-50"
                                >
                                    <div class="bg-white rounded-md shadow-lg py-1">
                                        <Link href="/orders" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50">Đơn hàng của tôi</Link>
                                        <Link href="/loyalty" class="flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-amber-50">
                                            <span>Điểm thưởng</span>
                                            <span v-if="page.props.auth.loyalty_points > 0" class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">
                                                {{ page.props.auth.loyalty_tier_icon }} {{ page.props.auth.loyalty_points }}
                                            </span>
                                        </Link>
                                        <Link href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50">Hồ sơ</Link>
                                        <Link v-if="page.props.auth.user.role === 'admin' || page.props.auth.user.role === 'staff'" href="/admin" class="block px-4 py-2 text-sm text-gray-700 hover:bg-amber-50">Quản trị</Link>
                                        <Link href="/logout" method="post" as="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-amber-50">Đăng xuất</Link>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <Link href="/login" class="text-sm text-amber-200 hover:text-white transition">Đăng nhập</Link>
                            <Link href="/register" class="text-sm bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-500 transition">Đăng ký</Link>
                        </template>

                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="sm:hidden text-amber-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div v-if="mobileMenuOpen" class="sm:hidden bg-amber-800 pb-3">
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    class="block px-4 py-2 text-amber-200 hover:text-white"
                    @click="mobileMenuOpen = false"
                >
                    {{ item.name }}
                </Link>
            </div>
        </nav>

        <!-- Main content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-amber-900 text-amber-200 mt-16">
            <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-lg font-bold text-white mb-4">☕ Coffee Shop</h3>
                        <p class="text-sm">Thưởng thức hương vị cà phê đích thực, pha chế từ những hạt cà phê được chọn lọc kỹ lưỡng.</p>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white mb-4">Liên kết</h3>
                        <ul class="space-y-2 text-sm">
                            <li><Link href="/menu" class="hover:text-white transition">Thực đơn</Link></li>
                            <li><Link href="/cart" class="hover:text-white transition">Giỏ hàng</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white mb-4">Liên hệ</h3>
                        <ul class="space-y-2 text-sm">
                            <li>📍 123 Nguyễn Huệ, Q.1, TP.HCM</li>
                            <li>📞 0901 234 567</li>
                            <li>✉️ info@coffeeshop.vn</li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-amber-800 mt-8 pt-8 text-center text-sm">
                    <p>&copy; {{ new Date().getFullYear() }} Coffee Shop. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>
