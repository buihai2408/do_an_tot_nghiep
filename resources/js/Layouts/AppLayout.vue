<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ChatBot from '@/Components/ChatBot.vue';
import ToastNotification from '@/Components/ToastNotification.vue';

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
    <div class="min-h-screen overflow-x-hidden" style="background:#FAF6F0; font-family:'Inter',sans-serif;">
        <ToastNotification />

        
        <nav style="background:#2C1810; border-bottom:1px solid #5C3A1E;" class="sticky top-0 z-50">
            <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16 lg:h-20">

                    
                    <Link href="/" class="flex items-center gap-2.5 group">
                        
                        <div style="background:#D4A853; border-radius:8px;" class="w-8 h-8 flex items-center justify-center flex-shrink-0 transition group-hover:scale-105">
                            <svg class="w-5 h-5" style="color:#2C1810;" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-white tracking-wider" style="font-family:'Playfair Display',serif;">Trạm Cà Phê</span>
                    </Link>

                    
                    <div class="hidden md:flex items-center space-x-8">
                        <Link
                            v-for="item in navigation"
                            :key="item.name"
                            :href="item.href"
                            class="text-sm font-medium tracking-widest uppercase transition-all duration-200"
                            style="color:rgba(255,255,255,0.7);"
                            onmouseover="this.style.color='#D4A853'"
                            onmouseout="this.style.color='rgba(255,255,255,0.7)'"
                        >
                            {{ item.name }}
                        </Link>
                    </div>

                    
                    <div class="flex items-center space-x-5">
                        
                        <Link href="/cart" class="relative transition" style="color:rgba(255,255,255,0.7);"
                            onmouseover="this.style.color='#D4A853'"
                            onmouseout="this.style.color='rgba(255,255,255,0.7)'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                            </svg>
                            <span v-if="page.props.cart_count > 0"
                                class="absolute -top-2 -right-2 text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center"
                                style="background:#D4A853; color:#2C1810;">
                                {{ page.props.cart_count }}
                            </span>
                        </Link>

                        
                        <template v-if="page.props.auth.user">
                            <div class="relative" @mouseenter="openDropdown" @mouseleave="closeDropdown">
                                <button class="flex items-center text-sm transition gap-1.5" style="color:rgba(255,255,255,0.85);">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold" style="background:#D4A853; color:#2C1810;">
                                        {{ page.props.auth.user.name?.charAt(0)?.toUpperCase() }}
                                    </div>
                                    <span class="hidden lg:inline">{{ page.props.auth.user.name }}</span>
                                    <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div v-show="userDropdownOpen" class="absolute right-0 top-full w-56 pt-2 z-50">
                                    <div class="shadow-xl border py-2" style="background:white; border-color:#E8D9C5; border-radius:4px;">
                                        <Link href="/orders" class="block px-5 py-2.5 text-sm transition hover:bg-amber-50" style="color:#2C1810;">📦 Đơn hàng của tôi</Link>
                                        <Link href="/loyalty" class="flex items-center justify-between px-5 py-2.5 text-sm transition hover:bg-amber-50" style="color:#2C1810;">
                                            <span>🏆 Điểm thưởng</span>
                                            <span v-if="page.props.auth.loyalty_points > 0" class="text-xs font-semibold px-2 py-0.5 rounded-full" style="background:#FEF3C7; color:#92400E;">
                                                {{ page.props.auth.loyalty_tier_icon }} {{ page.props.auth.loyalty_points }}
                                            </span>
                                        </Link>
                                        <Link href="/profile" class="block px-5 py-2.5 text-sm transition hover:bg-amber-50" style="color:#2C1810;">👤 Hồ sơ</Link>
                                        <Link v-if="page.props.auth.user.role === 'admin' || page.props.auth.user.role === 'staff'" href="/admin" class="block px-5 py-2.5 text-sm transition hover:bg-amber-50" style="color:#2C1810;">⚙️ Quản trị</Link>
                                        <hr style="border-color:#E8D9C5; margin:4px 0;">
                                        <Link href="/logout" method="post" as="button" class="block w-full text-left px-5 py-2.5 text-sm transition hover:bg-red-50" style="color:#991B1B;">Đăng xuất</Link>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <Link href="/login" class="hidden md:inline text-sm tracking-wider uppercase transition" style="color:rgba(255,255,255,0.7);"
                                onmouseover="this.style.color='#D4A853'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">Đăng nhập</Link>
                            <Link href="/register" class="hidden md:inline text-sm px-5 py-2 font-semibold tracking-wider uppercase transition rounded-sm"
                                style="background:#D4A853; color:#2C1810;"
                                onmouseover="this.style.background='#E8C17A'" onmouseout="this.style.background='#D4A853'">Đăng ký</Link>
                        </template>

                        
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden" style="color:rgba(255,255,255,0.8);">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            
            <div v-if="mobileMenuOpen" style="background:#2C1810; border-top:1px solid #5C3A1E;" class="md:hidden pb-4">
                <Link v-for="item in navigation" :key="item.name" :href="item.href"
                    class="block px-6 py-3 text-sm tracking-widest uppercase transition"
                    style="color:rgba(255,255,255,0.7);"
                    @click="mobileMenuOpen = false">
                    {{ item.name }}
                </Link>
                <template v-if="!page.props.auth.user">
                    <Link href="/login" class="block px-6 py-3 text-sm tracking-widest uppercase" style="color:rgba(255,255,255,0.7);" @click="mobileMenuOpen = false">Đăng nhập</Link>
                    <Link href="/register" class="block px-6 py-3 text-sm tracking-widest uppercase" style="color:#D4A853;" @click="mobileMenuOpen = false">Đăng ký</Link>
                </template>
            </div>
        </nav>

        
        <main>
            <slot />
        </main>

        
        <footer style="background:#1C1208; color:#B5A089;">
            <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                    
                    <div class="md:col-span-1">
                        <div class="flex items-center gap-2 mb-4">
                            <div style="background:#D4A853; border-radius:6px;" class="w-7 h-7 flex items-center justify-center">
                                <svg class="w-4 h-4" style="color:#2C1810;" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white" style="font-family:'Playfair Display',serif;">Trạm Cà Phê</h3>
                        </div>
                        <p class="text-sm leading-relaxed mb-5">Thưởng thức hương vị cà phê đích thực, pha chế từ những hạt cà phê được chọn lọc kỹ lưỡng.</p>
                        
                        <div class="flex gap-3">
                            <a href="#" class="w-8 h-8 rounded-full flex items-center justify-center transition hover:scale-110" style="background:#2C1810;">
                                <svg class="w-4 h-4" style="color:#D4A853;" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                            </a>
                            <a href="#" class="w-8 h-8 rounded-full flex items-center justify-center transition hover:scale-110" style="background:#2C1810;">
                                <svg class="w-4 h-4" style="color:#D4A853;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" stroke-width="2"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z" stroke-width="2"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke-width="2"/></svg>
                            </a>
                        </div>
                    </div>

                    
                    <div>
                        <h4 class="text-xs font-semibold tracking-widest uppercase mb-5" style="color:#D4A853;">Khám phá</h4>
                        <ul class="space-y-3 text-sm">
                            <li><Link href="/menu" class="hover:text-white transition">Menu</Link></li>
                            <li><Link href="/about" class="hover:text-white transition">Giới thiệu</Link></li>
                            <li><Link href="/contact" class="hover:text-white transition">Liên hệ</Link></li>
                        </ul>
                    </div>

                    
                    <div>
                        <h4 class="text-xs font-semibold tracking-widest uppercase mb-5" style="color:#D4A853;">Giờ mở cửa</h4>
                        <ul class="space-y-3 text-sm">
                            <li class="flex gap-2"><span style="color:#D4A853;">☕</span> Thứ 2 - Thứ 6: 07:00 - 22:00</li>
                            <li class="flex gap-2"><span style="color:#D4A853;">☕</span> Thứ 7 - CN: 08:00 - 23:00</li>
                        </ul>
                    </div>

                    
                    <div>
                        <h4 class="text-xs font-semibold tracking-widest uppercase mb-5" style="color:#D4A853;">Liên hệ</h4>
                        <ul class="space-y-3 text-sm">
                            <li class="flex gap-2 items-start"><span style="color:#D4A853;">📍</span> 175 Tây Sơn, Đống Đa, Hà Nội</li>
                            <li class="flex gap-2 items-center"><span style="color:#D4A853;">📞</span> 0966461728</li>
                            <li class="flex gap-2 items-center"><span style="color:#D4A853;">✉️</span> info@coffeeshop.vn</li>
                        </ul>
                    </div>
                </div>

                
                <div class="mt-12 pt-8 flex flex-col md:flex-row justify-between items-center text-xs" style="border-top:1px solid #2C1810; color:#6B5340;">
                    <p>© {{ new Date().getFullYear() }} Trạm Cà Phê. All rights reserved.</p>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <a href="#" class="hover:text-white transition">Chính sách bảo mật</a>
                        <a href="#" class="hover:text-white transition">Điều khoản sử dụng</a>
                    </div>
                </div>
            </div>
        </footer>

        
        <ChatBot />
    </div>
</template>
