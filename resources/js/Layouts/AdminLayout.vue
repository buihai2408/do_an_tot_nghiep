<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import axios from 'axios';

const page = usePage();
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
    { name: 'Người dùng', href: '/admin/users', icon: '👥', adminOnly: true },
    { name: 'Đánh giá', href: '/admin/reviews', icon: '⭐' },
    { name: 'Điểm thưởng', href: '/admin/users', icon: '💎', adminOnly: true },
    { name: 'Báo cáo', href: '/admin/reports', icon: '📈' },
];

const menuItems = computed(() =>
    allMenuItems.filter(item => !item.adminOnly || isAdmin.value)
);

const pendingCount = ref(0);
const pendingOrders = ref([]);
const showNotifPanel = ref(false);
const lastKnownCount = ref(0);
let pollInterval = null;
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

        if (data.count > lastKnownCount.value && lastKnownCount.value >= 0) {
            playNotifSound();
        }
        lastKnownCount.value = data.count;
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

onMounted(() => {
    lastKnownCount.value = -1;
    fetchNewOrders();
    pollInterval = setInterval(fetchNewOrders, 15000);
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    clearInterval(pollInterval);
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-16'" class="bg-amber-900 text-white transition-all duration-300 flex-shrink-0">
            <div class="p-4">
                <Link href="/admin" class="flex items-center space-x-2">
                    <span class="text-2xl">☕</span>
                    <span v-if="sidebarOpen" class="text-lg font-bold">Admin</span>
                </Link>
            </div>
            <nav class="mt-4">
                <Link
                    v-for="item in menuItems"
                    :key="item.name"
                    :href="item.href"
                    class="flex items-center px-4 py-3 text-amber-200 hover:bg-amber-800 hover:text-white transition"
                >
                    <span class="text-lg">{{ item.icon }}</span>
                    <span v-if="sidebarOpen" class="ml-3 text-sm">{{ item.name }}</span>
                </Link>
            </nav>
        </aside>

        <!-- Main area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top bar -->
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex items-center space-x-4">
                    <!-- Notification Bell -->
                    <div ref="notifPanelRef" class="relative">
                        <button
                            @click="toggleNotifPanel"
                            class="relative p-2 text-gray-500 hover:text-amber-600 transition"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span
                                v-if="pendingCount > 0"
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold animate-pulse"
                            >
                                {{ pendingCount > 9 ? '9+' : pendingCount }}
                            </span>
                        </button>

                        <!-- Notification Panel -->
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
                                class="absolute right-0 top-full mt-2 w-80 bg-white rounded-lg shadow-xl border z-50 origin-top-right"
                            >
                                <div class="px-4 py-3 border-b bg-amber-50 rounded-t-lg">
                                    <h3 class="font-semibold text-amber-900">Đơn hàng chờ xử lý</h3>
                                    <p class="text-xs text-amber-700 mt-0.5">{{ pendingCount }} đơn hàng đang chờ xác nhận</p>
                                </div>

                                <div v-if="pendingOrders.length > 0" class="max-h-72 overflow-y-auto">
                                    <button
                                        v-for="order in pendingOrders"
                                        :key="order.id"
                                        @click="goToOrder(order.id)"
                                        class="w-full text-left px-4 py-3 hover:bg-amber-50 border-b last:border-b-0 transition"
                                    >
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">#{{ order.order_number }}</p>
                                                <p class="text-xs text-gray-500 mt-0.5">{{ order.customer_name }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-semibold text-amber-700">{{ formatCurrency(order.total) }}</p>
                                                <p class="text-xs text-gray-400 mt-0.5">{{ formatTime(order.created_at) }}</p>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                                <div v-else class="px-4 py-6 text-center text-sm text-gray-400">
                                    Không có đơn hàng mới
                                </div>

                                <button
                                    @click="goToOrders"
                                    class="w-full px-4 py-2.5 text-center text-sm font-medium text-amber-700 hover:bg-amber-50 border-t rounded-b-lg transition"
                                >
                                    Xem tất cả đơn hàng →
                                </button>
                            </div>
                        </Transition>
                    </div>

                    <Link href="/" class="text-sm text-gray-500 hover:text-gray-700">← Về trang chủ</Link>
                    <span class="text-sm text-gray-700">{{ page.props.auth.user?.name }}</span>
                    <Link href="/logout" method="post" as="button" class="text-sm text-red-500 hover:text-red-700">Đăng xuất</Link>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 p-6 overflow-auto">
                <slot />
            </main>
        </div>
    </div>
</template>
