<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import axios from 'axios';
import ToastNotification from '@/Components/ToastNotification.vue';
import { useToast } from '@/Composables/useToast';

const page = usePage();
const { success, info } = useToast();
const sidebarOpen = ref(true);
const isAdmin = computed(() => page.props.auth.user?.role === 'admin');

const allMenuItems = [
    { name: 'Dashboard', href: '/admin', icon: '📊' },
    { name: 'Danh mục', href: '/admin/categories', icon: '📂' },
    { name: 'Sản phẩm', href: '/admin/products', icon: '☕' },
    { name: 'Đơn hàng', href: '/admin/orders', icon: '📋' },
    { name: 'Mã giảm giá', href: '/admin/coupons', icon: '🎫' },
    { name: 'Topping', href: '/admin/toppings', icon: '🧋' },
    { name: 'Kích thước', href: '/admin/sizes', icon: '📏' },
    { name: 'Người dùng & Điểm', href: '/admin/users', icon: '👥', adminOnly: true },
    { name: 'Đánh giá', href: '/admin/reviews', icon: '⭐' },
];

const menuItems = computed(() =>
    allMenuItems.filter(item => !item.adminOnly || isAdmin.value)
);

const pendingCount = ref(0);
const pendingOrders = ref([]);
const showNotifPanel = ref(false);
const notifPanelRef = ref(null);

function playNotifSound() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.frequency.value = 880;
        osc.type = 'sine';
        gain.gain.setValueAtTime(0.3, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.5);
    } catch {}
}

async function fetchNewOrders() {
    try {
        const { data } = await axios.get('/api/admin/orders/new');
        pendingOrders.value = data.orders;
        pendingCount.value = data.count;
    } catch {}
}

function toggleNotifPanel() {
    showNotifPanel.value = !showNotifPanel.value;
}

function goToOrder(orderId) {
    showNotifPanel.value = false;
    router.visit(`/admin/orders/${orderId}`);
}

function goToOrders() {
    showNotifPanel.value = false;
    router.visit('/admin/orders');
}

function handleClickOutside(e) {
    if (notifPanelRef.value && !notifPanelRef.value.contains(e.target)) {
        showNotifPanel.value = false;
    }
}

function formatTime(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
}

function formatCurrency(val) {
    return Number(val).toLocaleString('vi-VN') + 'đ';
}

function isActive(href) {
    const path = window.location.pathname;
    if (href === '/admin') {
        return path === '/admin' || path === '/admin/';
    }
    return path === href || path.startsWith(href + '/');
}

onMounted(() => {
    fetchNewOrders();
    document.addEventListener('click', handleClickOutside);

    if (window.Echo) {
        window.Echo.private('admin.orders')
            .listen('.NewOrderPlaced', (e) => {
                playNotifSound();
                info(`Đơn hàng mới: #${e.order.order_number}`);
                
                
                pendingOrders.value.unshift({
                    id: e.order.id,
                    order_number: e.order.order_number,
                    customer_name: e.order.customer_name,
                    total: e.order.total,
                    created_at: e.order.created_at,
                });
                
                
                if (pendingOrders.value.length > 10) {
                    pendingOrders.value.pop();
                }
                
                pendingCount.value++;
            });
    }
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    if (window.Echo) {
        window.Echo.leave('admin.orders');
    }
});
</script>

<template>
    <div class="min-h-screen bg-[#FAF6F0] flex font-sans">
        <ToastNotification />
        
        <aside :class="sidebarOpen ? 'w-64' : 'w-16'" class="bg-[#1C1208] text-white transition-all duration-300 flex-shrink-0 shadow-xl z-10 border-r border-[#2C1810]">
            
            <div class="p-5 border-b border-[#D4A853]/20 flex items-center justify-between">
                <Link href="/admin" class="flex items-center space-x-3 overflow-hidden">
                    <div class="w-8 h-8 rounded-lg bg-[#D4A853] flex items-center justify-center flex-shrink-0 text-[#1C1208]">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M2 21h18v-2H2v2zM20 8h-2V5h2v3zm2-4h-2V2a1 1 0 00-1-1H3a1 1 0 00-1 1v11a4 4 0 004 4h8a4 4 0 004-4v-1h2a2 2 0 002-2V6a2 2 0 00-2-2zm-4 9a2 2 0 01-2 2H6a2 2 0 01-2-2V3h14v10zm4-3h-2V6h2v4z"/>
                        </svg>
                    </div>
                    <div v-if="sidebarOpen" class="flex flex-col">
                        <span class="text-lg font-bold font-playfair tracking-wide text-white">Coffee Admin</span>
                        <span class="text-[10px] tracking-widest uppercase text-[#D4A853]">Portal</span>
                    </div>
                </Link>
            </div>
            
            
            <div v-if="sidebarOpen" class="px-5 py-3 border-b border-[#D4A853]/10">
                <span class="text-[10px] font-bold tracking-widest uppercase px-2 py-1 rounded"
                    :class="isAdmin ? 'bg-[#D4A853]/20 text-[#D4A853]' : 'bg-white/10 text-white/50'">
                    {{ isAdmin ? '👑 Admin' : '🧑‍💼 Staff' }}
                </span>
            </div>

            
            <nav class="mt-4 px-2 overflow-hidden">
                <Link
                    v-for="item in menuItems"
                    :key="item.name"
                    :href="item.href"
                    class="flex items-center px-3 py-2.5 mb-1 rounded-lg text-gray-400 hover:bg-[#2C1810] hover:text-[#FAF6F0] transition group relative"
                    :class="isActive(item.href) ? 'bg-[#2C1810] !text-[#D4A853]' : ''"
                >
                    <span class="text-lg w-8 text-center flex-shrink-0">{{ item.icon }}</span>
                    <span v-if="sidebarOpen" class="ml-2 text-sm font-medium whitespace-nowrap">{{ item.name }}</span>
                    
                    
                    <div v-if="!sidebarOpen" class="absolute left-full ml-2 px-2 py-1 text-xs font-medium rounded bg-[#D4A853] text-[#1C1208] opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50 whitespace-nowrap">
                        {{ item.name }}
                    </div>
                </Link>
            </nav>
        </aside>

        
        <div class="flex-1 flex flex-col min-w-0">
            
            <header class="bg-white border-b border-[#E8D9C5] h-16 flex items-center justify-between px-6 shadow-sm z-0">
                <button @click="sidebarOpen = !sidebarOpen" class="text-[#8B7355] hover:text-[#2C1810] hover:bg-[#FAF6F0] p-1.5 rounded transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex items-center gap-5">
                    
                    <div ref="notifPanelRef" class="relative">
                        <button
                            @click="toggleNotifPanel"
                            class="relative p-2 text-[#8B7355] hover:text-[#2C1810] hover:bg-[#FAF6F0] rounded transition"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span
                                v-if="pendingCount > 0"
                                class="absolute top-1 right-1 bg-[#D4A853] text-[#1C1208] text-[10px] rounded-full h-4 w-4 flex items-center justify-center font-bold animate-pulse"
                            >
                                {{ pendingCount > 9 ? '9+' : pendingCount }}
                            </span>
                        </button>

                        
                        <Transition
                            enter-active-class="transition ease-out duration-200"
                            enter-from-class="opacity-0 scale-95"
                            enter-to-class="opacity-100 scale-100"
                            leave-active-class="transition ease-in duration-150"
                            leave-from-class="opacity-100 scale-100"
                            leave-to-class="opacity-0 scale-95"
                        >
                            <div
                                v-show="showNotifPanel"
                                class="absolute right-0 top-full mt-2 w-80 bg-white shadow-xl border border-[#E8D9C5] rounded-lg z-50 origin-top-right overflow-hidden"
                            >
                                <div class="px-4 py-3 border-b border-[#E8D9C5] bg-[#FAF6F0]">
                                    <h3 class="font-semibold text-[#2C1810] text-sm">📋 Đơn hàng chờ xử lý</h3>
                                    <p class="text-xs text-[#8B7355] mt-0.5">{{ pendingCount }} đơn hàng đang chờ xác nhận</p>
                                </div>

                                <div v-if="pendingOrders.length > 0" class="max-h-72 overflow-y-auto">
                                    <button
                                        v-for="order in pendingOrders"
                                        :key="order.id"
                                        @click="goToOrder(order.id)"
                                        class="w-full text-left px-4 py-3 hover:bg-[#FAF6F0] border-b border-[#F2EBE0] last:border-b-0 transition"
                                    >
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-sm font-medium text-[#2C1810]">#{{ order.order_number }}</p>
                                                <p class="text-xs text-[#8B7355] mt-0.5">{{ order.customer_name }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-bold text-[#D4A853]">{{ formatCurrency(order.total) }}</p>
                                                <p class="text-xs text-[#B5A089] mt-0.5">{{ formatTime(order.created_at) }}</p>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                                <div v-else class="px-4 py-6 text-center text-sm text-[#B5A089]">
                                    Không có đơn hàng mới
                                </div>

                                <button
                                    @click="goToOrders"
                                    class="w-full px-4 py-3 text-center text-sm font-semibold text-[#2C1810] hover:bg-[#FAF6F0] border-t border-[#E8D9C5] transition"
                                >
                                    Xem tất cả đơn hàng →
                                </button>
                            </div>
                        </Transition>
                    </div>

                    <div class="w-px h-6 bg-[#E8D9C5]"></div>

                    <Link href="/" class="text-sm text-[#8B7355] hover:text-[#D4A853] transition">← Về trang chủ</Link>
                    
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-[#D4A853] text-[#1C1208] flex items-center justify-center font-bold text-sm">
                            {{ page.props.auth.user?.name?.charAt(0)?.toUpperCase() }}
                        </div>
                        <span class="text-sm text-[#2C1810] font-semibold hidden md:inline">{{ page.props.auth.user?.name }}</span>
                    </div>

                    <Link href="/logout" method="post" as="button" class="text-xs font-bold tracking-wider uppercase px-3 py-1.5 rounded border border-red-100 text-red-700 hover:bg-red-50 transition">
                        Đăng xuất
                    </Link>
                </div>
            </header>

            
            <main class="flex-1 p-6 overflow-auto">
                <slot />
            </main>
        </div>
    </div>
</template>
