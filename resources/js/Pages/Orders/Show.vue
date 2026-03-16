<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { useFormatters } from '@/Composables/useFormatters';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

const { formatCurrency } = useFormatters();

const props = defineProps({ order: Object });

const showCancelModal = ref(false);
const cancelReason = ref('');
const showReviewModal = ref(false);
const reviewForm = ref({ product_id: null, rating: 5, comment: '' });
const submitting = ref(false);

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

const submitReview = async () => {
    submitting.value = true;
    try {
        await axios.post(`/api/orders/${props.order.id}/review`, reviewForm.value);
        showReviewModal.value = false;
        router.reload();
    } catch (e) {
        alert(e.response?.data?.message || 'Không thể gửi đánh giá.');
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
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-amber-900">#{{ order.order_number }}</h1>
                    <span :class="statusColors[order.status_color]" class="px-4 py-2 rounded-full text-sm font-semibold">{{ order.status_label }}</span>
                </div>

                <!-- Timeline -->
                <div class="mb-8 text-sm text-gray-600 space-y-1">
                    <p>📅 Đặt hàng: {{ order.created_at }}</p>
                    <p v-if="order.confirmed_at">✅ Xác nhận: {{ order.confirmed_at }}</p>
                    <p v-if="order.completed_at">🎉 Hoàn thành: {{ order.completed_at }}</p>
                    <p v-if="order.cancelled_at">❌ Hủy: {{ order.cancelled_at }}</p>
                    <p v-if="order.cancel_reason" class="text-red-500">Lý do hủy: {{ order.cancel_reason }}</p>
                </div>

                <!-- Customer info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8 text-sm">
                    <div class="bg-amber-50 rounded-xl p-4">
                        <h3 class="font-semibold text-amber-900 mb-2">Thông tin giao hàng</h3>
                        <p>{{ order.customer_name }} - {{ order.customer_phone }}</p>
                        <p v-if="order.shipping_address" class="text-gray-600 mt-1">{{ order.shipping_address }}</p>
                        <p class="text-amber-600 mt-1">{{ order.order_type_label }}</p>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-4">
                        <h3 class="font-semibold text-amber-900 mb-2">Thanh toán</h3>
                        <p>{{ order.payment_method }}</p>
                        <p class="text-gray-600 mt-1">{{ order.payment_status }}</p>
                    </div>
                </div>

                <!-- Items -->
                <h2 class="text-lg font-bold text-amber-900 mb-4">Sản phẩm</h2>
                <div class="space-y-3 mb-6">
                    <div v-for="item in order.items" :key="item.id" class="flex justify-between items-start p-4 bg-gray-50 rounded-xl">
                        <div>
                            <p class="font-medium">{{ item.product_name }}</p>
                            <p class="text-sm text-gray-500">
                                <span v-if="item.size_name">Size {{ item.size_name }}</span>
                                <span v-if="item.ice_level"> · Đá: {{ item.ice_level }}</span>
                                <span v-if="item.sugar_level"> · Đường: {{ item.sugar_level }}</span>
                            </p>
                            <p v-if="item.toppings.length" class="text-xs text-gray-400 mt-1">
                                + {{ item.toppings.map(t => t.name).join(', ') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold">{{ formatCurrency(item.subtotal) }}</p>
                            <p class="text-sm text-gray-500">x{{ item.quantity }}</p>
                        </div>
                    </div>
                </div>

                <!-- Totals -->
                <div class="border-t pt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-600">Tạm tính</span><span>{{ formatCurrency(order.subtotal) }}</span></div>
                    <div v-if="order.discount_amount > 0" class="flex justify-between text-green-600"><span>Giảm giá (coupon)</span><span>-{{ formatCurrency(order.discount_amount) }}</span></div>
                    <div v-if="order.points_discount > 0" class="flex justify-between text-green-600"><span>Giảm giá ({{ order.points_used }} điểm)</span><span>-{{ formatCurrency(order.points_discount) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-600">Phí giao hàng</span><span>{{ order.shipping_fee == 0 ? 'Miễn phí' : formatCurrency(order.shipping_fee) }}</span></div>
                    <hr />
                    <div class="flex justify-between text-xl font-bold text-amber-900"><span>Tổng cộng</span><span>{{ formatCurrency(order.total) }}</span></div>
                </div>

                <div v-if="order.points_earned > 0" class="mt-4 flex items-center space-x-2 text-sm bg-green-50 text-green-700 p-3 rounded-lg">
                    <span class="text-lg">🎁</span>
                    <span>Bạn đã nhận được <strong>+{{ order.points_earned }}</strong> điểm thưởng từ đơn hàng này!</span>
                </div>

                <p v-if="order.note" class="mt-4 text-sm text-gray-500 bg-amber-50 p-3 rounded-lg">📝 {{ order.note }}</p>

                <!-- Actions -->
                <div class="mt-6 flex space-x-4">
                    <button v-if="order.can_cancel" @click="showCancelModal = true" class="px-6 py-2 bg-red-50 text-red-600 rounded-xl font-medium hover:bg-red-100 transition">
                        Hủy đơn hàng
                    </button>
                    <button v-if="order.can_review" @click="showReviewModal = true; reviewForm.product_id = order.items[0]?.product_id" class="px-6 py-2 bg-amber-100 text-amber-700 rounded-xl font-medium hover:bg-amber-200 transition">
                        Đánh giá
                    </button>
                </div>
            </div>
        </div>

        <!-- Cancel Modal -->
        <div v-if="showCancelModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-bold text-amber-900 mb-4">Hủy đơn hàng</h3>
                <textarea v-model="cancelReason" rows="3" placeholder="Lý do hủy..." class="w-full rounded-lg border-gray-300 focus:border-amber-500 mb-4"></textarea>
                <div class="flex space-x-3">
                    <button @click="showCancelModal = false" class="flex-1 px-4 py-2 border rounded-xl">Đóng</button>
                    <button @click="cancelOrder" :disabled="!cancelReason || submitting" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-xl disabled:opacity-50">Xác nhận hủy</button>
                </div>
            </div>
        </div>

        <!-- Review Modal -->
        <div v-if="showReviewModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-bold text-amber-900 mb-4">Đánh giá đơn hàng</h3>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Đánh giá</label>
                    <div class="flex space-x-2">
                        <button v-for="star in 5" :key="star" @click="reviewForm.rating = star" class="text-2xl" :class="star <= reviewForm.rating ? 'text-yellow-400' : 'text-gray-300'">★</button>
                    </div>
                </div>
                <textarea v-model="reviewForm.comment" rows="3" placeholder="Nhận xét..." class="w-full rounded-lg border-gray-300 focus:border-amber-500 mb-4"></textarea>
                <div class="flex space-x-3">
                    <button @click="showReviewModal = false" class="flex-1 px-4 py-2 border rounded-xl">Đóng</button>
                    <button @click="submitReview" :disabled="submitting" class="flex-1 px-4 py-2 bg-amber-700 text-white rounded-xl disabled:opacity-50">Gửi đánh giá</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
