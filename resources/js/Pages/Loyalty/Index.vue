<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useFormatters } from '@/Composables/useFormatters';

const { formatCurrency } = useFormatters();

const props = defineProps({
    summary: Object,
    tiers: Array,
    history: Object,
});

const tierBgColors = {
    amber: 'from-amber-600 to-amber-800',
    gray: 'from-gray-500 to-gray-700',
    yellow: 'from-yellow-500 to-yellow-700',
    cyan: 'from-cyan-500 to-cyan-700',
};

const progressPercent = () => {
    if (!props.summary.next_tier) return 100;
    const currentMin = props.tiers.find(t => t.value === props.summary.tier)?.min_points || 0;
    const nextMin = props.summary.next_tier.min_points;
    const range = nextMin - currentMin;
    if (range <= 0) return 100;
    const progress = props.summary.total_earned - currentMin;
    return Math.min(100, Math.round((progress / range) * 100));
};
</script>

<template>
    <AppLayout>
        <!-- Page Header -->
        <section class="bg-[#1a1a1a] py-16 lg:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl lg:text-5xl font-bold text-white" style="font-family: 'Playfair Display', serif;">Điểm thưởng</h1>
            </div>
        </section>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Current Tier Card -->
            <div :class="tierBgColors[summary.tier_color]" class="bg-gradient-to-r text-white p-8 lg:p-10 mb-10">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/70 text-xs tracking-widest uppercase mb-2">Hạng thành viên</p>
                        <div class="flex items-center space-x-3">
                            <span class="text-4xl">{{ summary.tier_icon }}</span>
                            <div>
                                <h2 class="text-2xl font-bold" style="font-family: 'Playfair Display', serif;">{{ summary.tier_label }}</h2>
                                <p class="text-white/70 text-sm">Hệ số nhân: x{{ summary.multiplier }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-white/70 text-xs tracking-widest uppercase mb-1">Điểm hiện có</p>
                        <p class="text-4xl font-bold">{{ summary.points.toLocaleString() }}</p>
                        <p class="text-white/70 text-sm">~ {{ formatCurrency(summary.points * 1000) }}</p>
                    </div>
                </div>

                <div v-if="summary.next_tier" class="mt-8">
                    <div class="flex justify-between text-sm text-white/70 mb-2">
                        <span>{{ summary.tier_label }}</span>
                        <span>{{ summary.next_tier.icon }} {{ summary.next_tier.label }} ({{ summary.next_tier.min_points.toLocaleString() }} điểm)</span>
                    </div>
                    <div class="w-full bg-white/20 h-2">
                        <div class="bg-white h-2 transition-all duration-500" :style="{ width: progressPercent() + '%' }"></div>
                    </div>
                    <p class="text-sm text-white/70 mt-2">Cần thêm <strong>{{ summary.next_tier.points_needed.toLocaleString() }}</strong> điểm để lên hạng</p>
                </div>
                <div v-else class="mt-6">
                    <p class="text-white/70">Bạn đã đạt hạng cao nhất!</p>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-10">
                <div class="bg-[#f8f5f0] p-6 text-center">
                    <p class="text-3xl font-bold text-[#1a1a1a]">{{ summary.points.toLocaleString() }}</p>
                    <p class="text-xs text-gray-500 mt-1 tracking-wider uppercase">Khả dụng</p>
                </div>
                <div class="bg-[#f8f5f0] p-6 text-center">
                    <p class="text-3xl font-bold text-[#1a1a1a]">{{ summary.total_earned.toLocaleString() }}</p>
                    <p class="text-xs text-gray-500 mt-1 tracking-wider uppercase">Tổng tích lũy</p>
                </div>
                <div class="bg-[#f8f5f0] p-6 text-center col-span-2 md:col-span-1">
                    <p class="text-3xl font-bold text-[#1a1a1a]">x{{ summary.multiplier }}</p>
                    <p class="text-xs text-gray-500 mt-1 tracking-wider uppercase">Hệ số nhân</p>
                </div>
            </div>

            <!-- Tier Benefits Table -->
            <div class="bg-white border border-gray-100 p-8 mb-10">
                <h2 class="text-xs font-semibold tracking-widest uppercase text-[#1a1a1a] mb-6">Quyền lợi theo hạng</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-2 text-xs tracking-widest uppercase text-gray-500">Hạng</th>
                                <th class="text-center py-3 px-2 text-xs tracking-widest uppercase text-gray-500">Điểm tối thiểu</th>
                                <th class="text-center py-3 px-2 text-xs tracking-widest uppercase text-gray-500">Hệ số</th>
                                <th class="text-center py-3 px-2 text-xs tracking-widest uppercase text-gray-500">Tích điểm / 100k đ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="tier in tiers"
                                :key="tier.value"
                                :class="tier.value === summary.tier ? 'bg-[#f8f5f0] font-semibold' : ''"
                                class="border-b border-gray-100 last:border-0"
                            >
                                <td class="py-3 px-2">
                                    <span class="mr-2">{{ tier.icon }}</span>{{ tier.label }}
                                    <span v-if="tier.value === summary.tier" class="ml-2 text-[10px] bg-[#1a1a1a] text-white px-2 py-0.5">Hiện tại</span>
                                </td>
                                <td class="text-center py-3 px-2">{{ tier.min_points.toLocaleString() }}</td>
                                <td class="text-center py-3 px-2">x{{ tier.multiplier }}</td>
                                <td class="text-center py-3 px-2">{{ Math.floor(10 * tier.multiplier) }} điểm</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- How it works -->
            <div class="bg-white border border-gray-100 p-8 mb-10">
                <h2 class="text-xs font-semibold tracking-widest uppercase text-[#1a1a1a] mb-6">Cách thức hoạt động</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4">
                        <div class="w-14 h-14 mx-auto mb-4 bg-[#f8f5f0] flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#1a1a1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <h3 class="font-semibold text-sm text-[#1a1a1a] mb-1">Mua hàng</h3>
                        <p class="text-xs text-gray-500">Mỗi 10.000đ = 1 điểm (nhân hệ số hạng)</p>
                    </div>
                    <div class="text-center p-4">
                        <div class="w-14 h-14 mx-auto mb-4 bg-[#f8f5f0] flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#1a1a1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                        </div>
                        <h3 class="font-semibold text-sm text-[#1a1a1a] mb-1">Đổi điểm</h3>
                        <p class="text-xs text-gray-500">1 điểm = 1.000đ giảm giá (tối đa 30% đơn)</p>
                    </div>
                    <div class="text-center p-4">
                        <div class="w-14 h-14 mx-auto mb-4 bg-[#f8f5f0] flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#1a1a1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                        <h3 class="font-semibold text-sm text-[#1a1a1a] mb-1">Lên hạng</h3>
                        <p class="text-xs text-gray-500">Tích lũy đủ điểm để nâng hạng thành viên</p>
                    </div>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="bg-white border border-gray-100 p-8">
                <h2 class="text-xs font-semibold tracking-widest uppercase text-[#1a1a1a] mb-6">Lịch sử điểm</h2>

                <div v-if="history.data.length === 0" class="text-center py-12 text-gray-400">
                    <p class="text-lg mb-2">Chưa có giao dịch điểm nào.</p>
                    <Link href="/menu" class="text-sm text-[#1a1a1a] font-medium border-b border-[#1a1a1a] pb-0.5 hover:text-amber-700 transition">Mua hàng ngay</Link>
                </div>

                <div v-else>
                    <div class="space-y-3">
                        <div v-for="tx in history.data" :key="tx.id" class="flex items-center justify-between p-4 border border-gray-100 hover:bg-[#f8f5f0] transition">
                            <div class="flex items-center gap-3">
                                <span :class="tx.type === 'earn' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'" class="w-10 h-10 flex items-center justify-center text-lg font-bold">
                                    {{ tx.type === 'earn' ? '+' : '-' }}
                                </span>
                                <div>
                                    <p class="font-medium text-sm text-[#1a1a1a]">{{ tx.description }}</p>
                                    <p class="text-xs text-gray-400">{{ tx.created_at }}</p>
                                </div>
                            </div>
                            <span :class="tx.type === 'earn' ? 'text-green-600' : 'text-red-600'" class="font-bold text-lg">
                                {{ tx.type === 'earn' ? '+' : '-' }}{{ tx.points.toLocaleString() }}
                            </span>
                        </div>
                    </div>

                    <div v-if="history.links?.length > 3" class="flex justify-center mt-8 space-x-1">
                        <template v-for="link in history.links" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                v-html="link.label"
                                class="px-4 py-2 text-sm border transition"
                                :class="link.active ? 'bg-[#1a1a1a] text-white border-[#1a1a1a]' : 'bg-white text-gray-600 border-gray-300 hover:border-[#1a1a1a]'"
                            />
                            <span v-else v-html="link.label" class="px-4 py-2 text-sm text-gray-300" />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
