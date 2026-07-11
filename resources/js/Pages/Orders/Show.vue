<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted, onUnmounted } from 'vue';
import { useFormatters } from '@/Composables/useFormatters';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import { useToast } from '@/Composables/useToast';

const { formatCurrency } = useFormatters();
const { info } = useToast();

const props = defineProps({ order: Object });

const showCancelModal = ref(false);
const cancelReason = ref('');
const showReviewModal = ref(false);
const reviewForm = ref({ rating: 5, comment: '' });
const submitting = ref(false);
const reviewError = ref('');

const cancelOrder = async () => {
    submitting.value = true;
    try {
        await axios.post(`/api/orders/${props.order.id}/cancel`, { cancel_reason: cancelReason.value });
        showCancelModal.value = false;
        router.reload();
    } catch (e) {
        alert(e.response?.data?.message || 'Không thể hủy đơn hàng.');
    } finally {
        submitting.value = false;
    }
};

const openReviewModal = () => {
    reviewForm.value = { rating: 5, comment: '' };
    reviewError.value = '';
    showReviewModal.value = true;
};

const submitReview = async () => {
    reviewError.value = '';
    submitting.value = true;
    try {
        await axios.post(`/api/orders/${props.order.id}/review`, reviewForm.value);
        showReviewModal.value = false;
        router.reload();
    } catch (e) {
        const data = e.response?.data;
        if (data?.errors) {
            reviewError.value = Object.values(data.errors).flat().join(' ');
        } else {
            reviewError.value = data?.message || 'Không thể gửi đánh giá.';
        }
    } finally {
        submitting.value = false;
    }
};

const statusColors = {
    yellow: 'bg-yellow-100 text-yellow-800',
    blue: 'bg-blue-100 text-blue-800',
    indigo: 'bg-indigo-100 text-indigo-800',
    purple: 'bg-purple-100 text-purple-800',
    orange: 'bg-orange-100 text-orange-800',
    green: 'bg-green-100 text-green-800',
    red: 'bg-red-100 text-red-800',
};

onMounted(() => {
    if (window.Echo) {
        window.Echo.private(`orders.${props.order.id}`)
            .listen('.OrderStatusUpdated', (e) => {
                info(`Trạng thái đơn hàng: ${e.orderData.status_label}`);
                router.reload({ only: ['order'] });
            });
    }
});

onUnmounted(() => {
    if (window.Echo) {
        window.Echo.leave(`orders.${props.order.id}`);
    }
});
</script>

<template>
    <AppLayout>
        
        <section class="bg-[#1a1a1a] py-16 lg:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl lg:text-5xl font-bold text-white" style="font-family: 'Playfair Display', serif;">Chi tiết đơn hàng</h1>
            </div>
        </section>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="bg-white border border-gray-100 p-8 lg:p-10">
                <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-[#1a1a1a]" style="font-family: 'Playfair Display', serif;">#{{ order.order_number }}</h2>
                    <span :class="statusColors[order.status_color]" class="px-4 py-1.5 text-xs font-semibold">{{ order.status_label }}</span>
                </div>

                
                <div class="mb-8 text-sm text-gray-500 space-y-1.5">
                    <p>Đặt hàng: {{ order.created_at }}</p>
                    <p v-if="order.confirmed_at">Xác nhận: {{ order.confirmed_at }}</p>
                    <p v-if="order.completed_at">Hoàn thành: {{ order.completed_at }}</p>
                    <p v-if="order.cancelled_at" class="text-red-500">Hủy: {{ order.cancelled_at }}</p>
                    <p v-if="order.cancel_reason" class="text-red-500">Lý do: {{ order.cancel_reason }}</p>
                </div>

                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <div class="bg-[#f8f5f0] p-5">
                        <h3 class="text-xs font-semibold tracking-widest uppercase text-[#1a1a1a] mb-3">Thông tin giao hàng</h3>
                        <p class="text-sm">{{ order.customer_name }} — {{ order.customer_phone }}</p>
                        <p v-if="order.shipping_address" class="text-sm text-gray-500 mt-1">{{ order.shipping_address }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ order.order_type_label }}</p>
                    </div>
                    <div class="bg-[#f8f5f0] p-5">
                        <h3 class="text-xs font-semibold tracking-widest uppercase text-[#1a1a1a] mb-3">Thanh toán</h3>
                        <p class="text-sm">{{ order.payment_method }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ order.payment_status }}</p>
                    </div>
                </div>

                
                <h3 class="text-xs font-semibold tracking-widest uppercase text-[#1a1a1a] mb-4">Sản phẩm</h3>
                <div class="space-y-3 mb-8">
                    <div v-for="item in order.items" :key="item.id" class="flex justify-between items-start p-4 bg-[#f8f5f0]">
                        <div>
                            <p class="font-medium text-sm text-[#1a1a1a]">{{ item.product_name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                <span v-if="item.size_name">Size {{ item.size_name }}</span>
                                <span v-if="item.ice_level"> · Đá: {{ item.ice_level }}</span>
                                <span v-if="item.sugar_level"> · Đường: {{ item.sugar_level }}</span>
                            </p>
                            <p v-if="item.toppings.length" class="text-[10px] text-gray-400 mt-0.5">
                                + {{ item.toppings.map(t => t.name).join(', ') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-sm">{{ formatCurrency(item.subtotal) }}</p>
                            <p class="text-xs text-gray-500">x{{ item.quantity }}</p>
                        </div>
                    </div>
                </div>

                
                <div class="border-t border-gray-200 pt-6 space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Tạm tính</span><span>{{ formatCurrency(order.subtotal) }}</span></div>
                    <div v-if="order.discount_amount > 0" class="flex justify-between text-green-600"><span>Giảm giá (coupon)</span><span>-{{ formatCurrency(order.discount_amount) }}</span></div>
                    <div v-if="order.points_discount > 0" class="flex justify-between text-green-600"><span>Giảm giá ({{ order.points_used }} điểm)</span><span>-{{ formatCurrency(order.points_discount) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Phí giao hàng</span><span>{{ order.shipping_fee == 0 ? 'Miễn phí' : formatCurrency(order.shipping_fee) }}</span></div>
                    <hr class="border-gray-200">
                    <div class="flex justify-between text-xl font-bold text-[#1a1a1a]"><span>Tổng cộng</span><span>{{ formatCurrency(order.total) }}</span></div>
                </div>

                <div v-if="order.points_earned > 0" class="mt-6 flex items-center gap-2 text-sm bg-green-50 text-green-700 p-4 border border-green-200">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                    <span>Bạn đã nhận được <strong>+{{ order.points_earned }}</strong> điểm thưởng từ đơn hàng này!</span>
                </div>

                <p v-if="order.note" class="mt-4 text-sm text-gray-500 bg-[#f8f5f0] p-4">Ghi chú: {{ order.note }}</p>

                
                <div class="mt-8 flex gap-4">
                    <button v-if="order.can_cancel" @click="showCancelModal = true" class="px-6 py-2.5 border border-red-300 text-red-600 text-sm font-medium hover:bg-red-50 transition">
                        Hủy đơn hàng
                    </button>
                    <button v-if="order.can_review" @click="openReviewModal()" class="px-6 py-2.5 bg-[#1a1a1a] text-white text-sm font-medium tracking-wider uppercase hover:bg-[#333] transition">
                        Đánh giá
                    </button>
                </div>
            </div>
        </div>

        
        <div v-if="showCancelModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white p-8 max-w-md w-full mx-4">
                <h3 class="text-lg font-bold text-[#1a1a1a] mb-4" style="font-family: 'Playfair Display', serif;">Hủy đơn hàng</h3>
                <textarea v-model="cancelReason" rows="3" placeholder="Lý do hủy..." class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] text-sm mb-4"></textarea>
                <div class="flex gap-3">
                    <button @click="showCancelModal = false" class="flex-1 px-4 py-2.5 border border-gray-300 text-sm font-medium hover:bg-gray-50 transition">Đóng</button>
                    <button @click="cancelOrder" :disabled="!cancelReason || submitting" class="flex-1 px-4 py-2.5 bg-red-600 text-white text-sm font-medium disabled:opacity-50 hover:bg-red-700 transition">Xác nhận hủy</button>
                </div>
            </div>
        </div>

        
        <div v-if="showReviewModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white p-8 max-w-md w-full mx-4">
                <h3 class="text-lg font-bold text-[#1a1a1a] mb-4" style="font-family: 'Playfair Display', serif;">Đánh giá đơn hàng #{{ order.order_number }}</h3>
                <div v-if="reviewError" class="mb-4 p-3 bg-red-50 text-red-600 text-sm border border-red-200">{{ reviewError }}</div>
                <div class="mb-4">
                    <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Đánh giá</label>
                    <div class="flex space-x-2">
                        <button v-for="star in 5" :key="star" @click="reviewForm.rating = star" class="text-2xl transition" :class="star <= reviewForm.rating ? 'text-amber-500' : 'text-gray-300'">★</button>
                    </div>
                </div>
                <textarea v-model="reviewForm.comment" rows="3" placeholder="Nhận xét..." class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] text-sm mb-4"></textarea>
                <div class="flex gap-3">
                    <button @click="showReviewModal = false" class="flex-1 px-4 py-2.5 border border-gray-300 text-sm font-medium hover:bg-gray-50 transition">Đóng</button>
                    <button @click="submitReview" :disabled="submitting" class="flex-1 px-4 py-2.5 bg-[#1a1a1a] text-white text-sm font-medium tracking-wider uppercase disabled:opacity-50 hover:bg-[#333] transition">Gửi đánh giá</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
