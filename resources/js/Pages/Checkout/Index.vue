<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { useFormatters } from '@/Composables/useFormatters';
import axios from 'axios';

const { formatCurrency } = useFormatters();
const page = usePage();

const props = defineProps({
    cart: Object,
    summary: Object,
    addresses: Array,
    order_types: Array,
    payment_methods: Array,
    loyalty: Object,
});

const form = ref({
    order_type: 'delivery',
    payment_method: 'cod',
    customer_name: page.props.auth.user?.name || '',
    customer_phone: page.props.auth.user?.phone || '',
    customer_email: page.props.auth.user?.email || '',
    shipping_address: '',
    coupon_code: '',
    points_used: 0,
    note: '',
});

const selectedAddress = ref(null);
const couponResult = ref(null);
const discount = ref(0);
const submitting = ref(false);
const errors = ref({});

const selectAddress = (address) => {
    selectedAddress.value = address.id;
    form.value.shipping_address = address.full_address || `${address.address_line}, ${address.ward || ''}, ${address.district || ''}, ${address.city || ''}`.replace(/, ,/g, ',').replace(/,\s*$/, '');
    form.value.customer_name = address.recipient_name;
    form.value.customer_phone = address.phone;
};

const pointsDiscount = computed(() => form.value.points_used * 1000);

const total = computed(() => {
    const shippingFee = form.value.order_type === 'pickup' ? 0 : Number(props.summary.shipping_fee);
    return Number(props.summary.subtotal) - discount.value - pointsDiscount.value + shippingFee;
});

const applyCoupon = async () => {
    try {
        const res = await axios.post('/api/checkout/apply-coupon', { code: form.value.coupon_code });
        couponResult.value = res.data;
        discount.value = res.data.discount || 0;
    } catch (e) {
        couponResult.value = e.response?.data;
        discount.value = 0;
    }
};

const submit = async () => {
    submitting.value = true;
    errors.value = {};
    try {
        const res = await axios.post('/api/checkout', form.value);
        router.visit(res.data.redirect || `/orders/${res.data.order.id}`);
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors || {};
        }
    } finally {
        submitting.value = false;
    }
};
</script>

<template>
    <AppLayout>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold text-amber-900 mb-8">Thanh toán</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Form -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Type -->
                    <div class="bg-white rounded-2xl shadow-md p-6">
                        <h2 class="text-lg font-bold text-amber-900 mb-4">Hình thức nhận hàng</h2>
                        <div class="flex space-x-4">
                            <button
                                v-for="type in order_types"
                                :key="type.value"
                                @click="form.order_type = type.value"
                                :class="form.order_type === type.value ? 'bg-amber-700 text-white' : 'bg-amber-100 text-amber-700'"
                                class="px-6 py-3 rounded-xl font-medium transition flex-1"
                            >
                                {{ type.label }}
                            </button>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="bg-white rounded-2xl shadow-md p-6">
                        <h2 class="text-lg font-bold text-amber-900 mb-4">Thông tin người nhận</h2>

                        <!-- Saved Addresses -->
                        <div v-if="addresses.length > 0 && form.order_type === 'delivery'" class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Chọn địa chỉ đã lưu:</p>
                            <div class="space-y-2">
                                <button
                                    v-for="addr in addresses"
                                    :key="addr.id"
                                    @click="selectAddress(addr)"
                                    :class="selectedAddress === addr.id ? 'border-amber-500 bg-amber-50' : 'border-gray-200'"
                                    class="w-full text-left p-3 rounded-lg border transition"
                                >
                                    <p class="font-medium">{{ addr.recipient_name }} - {{ addr.phone }}</p>
                                    <p class="text-sm text-gray-500">{{ addr.address_line }}, {{ addr.ward }}, {{ addr.district }}, {{ addr.city }}</p>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tên người nhận *</label>
                                <input v-model="form.customer_name" type="text" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500" />
                                <p v-if="errors.customer_name" class="text-red-500 text-sm mt-1">{{ errors.customer_name[0] }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại *</label>
                                <input v-model="form.customer_phone" type="text" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500" />
                                <p v-if="errors.customer_phone" class="text-red-500 text-sm mt-1">{{ errors.customer_phone[0] }}</p>
                            </div>
                        </div>

                        <div v-if="form.order_type === 'delivery'" class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ giao hàng *</label>
                            <textarea v-model="form.shipping_address" rows="2" class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500"></textarea>
                            <p v-if="errors.shipping_address" class="text-red-500 text-sm mt-1">{{ errors.shipping_address[0] }}</p>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                            <textarea v-model="form.note" rows="2" placeholder="Ví dụ: Giao trước 5h chiều..." class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500"></textarea>
                        </div>
                    </div>

                    <!-- Payment -->
                    <div class="bg-white rounded-2xl shadow-md p-6">
                        <h2 class="text-lg font-bold text-amber-900 mb-4">Phương thức thanh toán</h2>
                        <div class="space-y-2">
                            <label
                                v-for="method in payment_methods"
                                :key="method.value"
                                class="flex items-center p-4 rounded-xl border cursor-pointer transition"
                                :class="form.payment_method === method.value ? 'border-amber-500 bg-amber-50' : 'border-gray-200'"
                            >
                                <input v-model="form.payment_method" type="radio" :value="method.value" class="text-amber-600 focus:ring-amber-500" />
                                <span class="ml-3 font-medium">{{ method.label }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-white rounded-2xl shadow-md p-6 h-fit sticky top-24">
                    <h2 class="text-xl font-bold text-amber-900 mb-4">Đơn hàng</h2>
                    <div class="space-y-3 mb-4">
                        <div v-for="item in cart.items" :key="item.id" class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ item.product_name }} x{{ item.quantity }}</span>
                            <span>{{ formatCurrency(item.total) }}</span>
                        </div>
                    </div>

                    <!-- Coupon -->
                    <div class="mb-4">
                        <div class="flex space-x-2">
                            <input v-model="form.coupon_code" type="text" placeholder="Mã giảm giá" class="flex-1 rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm" />
                            <button @click="applyCoupon" class="px-4 py-2 bg-amber-100 text-amber-700 rounded-lg text-sm font-medium hover:bg-amber-200 transition">Áp dụng</button>
                        </div>
                        <p v-if="couponResult" :class="couponResult.valid ? 'text-green-600' : 'text-red-500'" class="text-sm mt-1">{{ couponResult.message }}</p>
                    </div>

                    <!-- Loyalty Points -->
                    <div v-if="loyalty && loyalty.points > 0" class="mb-4 p-4 bg-amber-50 rounded-xl border border-amber-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-amber-900">
                                {{ loyalty.tier.tier_icon }} Đổi điểm thưởng
                            </span>
                            <span class="text-xs text-amber-700">{{ loyalty.points.toLocaleString() }} điểm khả dụng</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <input
                                v-model.number="form.points_used"
                                type="range"
                                :min="0"
                                :max="loyalty.max_redeemable"
                                :step="1"
                                class="flex-1 h-2 bg-amber-200 rounded-lg appearance-none cursor-pointer accent-amber-700"
                            />
                            <span class="text-sm font-semibold text-amber-900 min-w-[4rem] text-right">{{ form.points_used }} điểm</span>
                        </div>
                        <p v-if="form.points_used > 0" class="text-xs text-green-600 mt-1">
                            Giảm {{ formatCurrency(pointsDiscount) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Tối đa {{ loyalty.max_redeemable }} điểm (50% đơn hàng)</p>
                    </div>

                    <hr class="my-4" />
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tạm tính</span>
                            <span>{{ formatCurrency(summary.subtotal) }}</span>
                        </div>
                        <div v-if="discount > 0" class="flex justify-between text-green-600">
                            <span>Giảm giá (coupon)</span>
                            <span>-{{ formatCurrency(discount) }}</span>
                        </div>
                        <div v-if="pointsDiscount > 0" class="flex justify-between text-green-600">
                            <span>Giảm giá (điểm)</span>
                            <span>-{{ formatCurrency(pointsDiscount) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phí giao hàng</span>
                            <span>{{ form.order_type === 'pickup' ? 'Miễn phí' : (summary.shipping_fee === 0 ? 'Miễn phí' : formatCurrency(summary.shipping_fee)) }}</span>
                        </div>
                        <hr />
                        <div class="flex justify-between text-lg font-bold text-amber-900">
                            <span>Tổng cộng</span>
                            <span>{{ formatCurrency(total) }}</span>
                        </div>
                    </div>

                    <button
                        @click="submit"
                        :disabled="submitting"
                        class="w-full bg-amber-700 text-white py-3 rounded-xl font-semibold text-center hover:bg-amber-600 transition mt-6 disabled:opacity-50"
                    >
                        {{ submitting ? 'Đang xử lý...' : 'Đặt hàng' }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
