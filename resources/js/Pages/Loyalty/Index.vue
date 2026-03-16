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
    gray: 'from-gray-400 to-gray-600',
    yellow: 'from-yellow-500 to-yellow-700',
    cyan: 'from-cyan-500 to-cyan-700',
};

const tierBorderColors = {
    amber: 'border-amber-300',
    gray: 'border-gray-300',
    yellow: 'border-yellow-300',
    cyan: 'border-cyan-300',
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
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold text-amber-900 mb-8">Điểm thưởng</h1>

            <!-- Current Tier Card -->
            <div :class="tierBgColors[summary.tier_color]" class="bg-gradient-to-r text-white rounded-2xl shadow-lg p-8 mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/80 text-sm mb-1">Hạng thành viên</p>
                        <div class="flex items-center space-x-3">
                            <span class="text-4xl">{{ summary.tier_icon }}</span>
                            <div>
                                <h2 class="text-2xl font-bold">{{ summary.tier_label }}</h2>
                                <p class="text-white/80 text-sm">Hệ số nhân: x{{ summary.multiplier }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-white/80 text-sm">Điểm hiện có</p>
                        <p class="text-4xl font-bold">{{ summary.points.toLocaleString() }}</p>
                        <p class="text-white/80 text-sm">~ {{ formatCurrency(summary.points * 1000) }}</p>
                    </div>
                </div>

                <!-- Progress to next tier -->
                <div v-if="summary.next_tier" class="mt-6">
                    <div class="flex justify-between text-sm text-white/80 mb-2">
                        <span>{{ summary.tier_label }}</span>
                        <span>{{ summary.next_tier.icon }} {{ summary.next_tier.label }} ({{ summary.next_tier.min_points.toLocaleString() }} điểm)</span>
                    </div>
                    <div class="w-full bg-white/20 rounded-full h-3">
                        <div class="bg-white rounded-full h-3 transition-all duration-500" :style="{ width: progressPercent() + '%' }"></div>
                    </div>
                    <p class="text-sm text-white/80 mt-2">Cần thêm <strong>{{ summary.next_tier.points_needed.toLocaleString() }}</strong> điểm để lên hạng</p>
                </div>
                <div v-else class="mt-6">
                    <p class="text-white/80">Bạn đã đạt hạng cao nhất!</p>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-2xl shadow-md p-6 text-center">
                    <p class="text-3xl font-bold text-amber-700">{{ summary.points.toLocaleString() }}</p>
                    <p class="text-sm text-gray-500 mt-1">Điểm khả dụng</p>
                </div>
                <div class="bg-white rounded-2xl shadow-md p-6 text-center">
                    <p class="text-3xl font-bold text-amber-700">{{ summary.total_earned.toLocaleString() }}</p>
                    <p class="text-sm text-gray-500 mt-1">Tổng tích lũy</p>
                </div>
                <div class="bg-white rounded-2xl shadow-md p-6 text-center col-span-2 md:col-span-1">
                    <p class="text-3xl font-bold text-amber-700">x{{ summary.multiplier }}</p>
                    <p class="text-sm text-gray-500 mt-1">Hệ số nhân điểm</p>
                </div>
            </div>

            <!-- Tier Benefits Table -->
            <div class="bg-white rounded-2xl shadow-md p-6 mb-8">
                <h2 class="text-xl font-bold text-amber-900 mb-4">Quyền lợi theo hạng</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-3 px-2 text-gray-600">Hạng</th>
                                <th class="text-center py-3 px-2 text-gray-600">Điểm tối thiểu</th>
                                <th class="text-center py-3 px-2 text-gray-600">Hệ số nhân</th>
                                <th class="text-center py-3 px-2 text-gray-600">Tích điểm / 100.000đ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="tier in tiers"
                                :key="tier.value"
                                :class="tier.value === summary.tier ? 'bg-amber-50 font-semibold' : ''"
                                class="border-b last:border-0"
                            >
                                <td class="py-3 px-2">
                                    <span class="mr-2">{{ tier.icon }}</span>{{ tier.label }}
                                    <span v-if="tier.value === summary.tier" class="ml-2 text-xs bg-amber-200 text-amber-800 px-2 py-0.5 rounded-full">Hiện tại</span>
                                </td>
                                <td class="text-center py-3 px-2">{{ tier.min_points.toLocaleString() }}</td>
                                <td class="text-center py-3 px-2">x{{ tier.multiplier }}</td>
                                <td class="text-center py-3 px-2">{{ Math.floor(100 * tier.multiplier) }} điểm</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- How it works -->
            <div class="bg-white rounded-2xl shadow-md p-6 mb-8">
                <h2 class="text-xl font-bold text-amber-900 mb-4">Cách thức hoạt động</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4">
                        <p class="text-3xl mb-2">🛍️</p>
                        <h3 class="font-semibold text-amber-900 mb-1">Mua hàng</h3>
                        <p class="text-sm text-gray-500">Mỗi 1.000đ = 1 điểm (nhân hệ số hạng)</p>
                    </div>
                    <div class="text-center p-4">
                        <p class="text-3xl mb-2">🎁</p>
                        <h3 class="font-semibold text-amber-900 mb-1">Đổi điểm</h3>
                        <p class="text-sm text-gray-500">1 điểm = 1.000đ giảm giá (tối đa 50% đơn hàng)</p>
                    </div>
                    <div class="text-center p-4">
                        <p class="text-3xl mb-2">⬆️</p>
                        <h3 class="font-semibold text-amber-900 mb-1">Lên hạng</h3>
                        <p class="text-sm text-gray-500">Tích lũy đủ điểm để nâng hạng thành viên</p>
                    </div>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="bg-white rounded-2xl shadow-md p-6">
                <h2 class="text-xl font-bold text-amber-900 mb-4">Lịch sử điểm</h2>

                <div v-if="history.data.length === 0" class="text-center py-8 text-gray-500">
                    <p class="text-4xl mb-2">📋</p>
                    <p>Chưa có giao dịch điểm nào.</p>
                    <Link href="/menu" class="inline-block mt-4 text-amber-700 font-medium hover:text-amber-600">Mua hàng ngay →</Link>
                </div>

                <div v-else>
                    <div class="space-y-3">
                        <div v-for="tx in history.data" :key="tx.id" class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:bg-gray-50 transition">
                            <div class="flex items-center space-x-3">
                                <span :class="tx.type === 'earn' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold">
                                    {{ tx.type === 'earn' ? '+' : '-' }}
                                </span>
                                <div>
                                    <p class="font-medium text-gray-900">{{ tx.description }}</p>
                                    <p class="text-xs text-gray-500">{{ tx.created_at }}</p>
                                </div>
                            </div>
                            <span :class="tx.type === 'earn' ? 'text-green-600' : 'text-red-600'" class="font-bold text-lg">
                                {{ tx.type === 'earn' ? '+' : '-' }}{{ tx.points.toLocaleString() }}
                            </span>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="history.links?.length > 3" class="flex justify-center mt-6 space-x-1">
                        <template v-for="link in history.links" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                v-html="link.label"
                                class="px-4 py-2 rounded-lg text-sm"
                                :class="link.active ? 'bg-amber-700 text-white' : 'bg-white text-amber-700 hover:bg-amber-100'"
                            />
                            <span v-else v-html="link.label" class="px-4 py-2 text-sm text-gray-400" />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
