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
    save_address: false,
    address_label: '',
});

const selectedAddress = ref(null);
const couponResult = ref(null);
const discount = ref(0);
const submitting = ref(false);
const errors = ref({});

const selectAddress = (address) => {
    selectedAddress.value = address.id;
    form.value.shipping_address = address.full_address || address.address_line;
    form.value.customer_name = address.recipient_name;
    form.value.customer_phone = address.phone;
};

const pointsDiscount = computed(() => form.value.points_used * 1000);
const POINTS_VALUE = 1000;

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
        const redirect = res.data.redirect || `/orders/${res.data.order.id}`;

        if (redirect.startsWith('http') && !redirect.startsWith(window.location.origin)) {
            window.location.href = redirect;
        } else {
            router.visit(redirect);
        }
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors || {};
            if (e.response.data.message && !e.response.data.errors) {
                errors.value = { general: [e.response.data.message] };
            }
        }
    } finally {
        submitting.value = false;
    }
};
</script>

<template>
    <AppLayout>
        <!-- Page Header -->
        <section class="bg-[#1a1a1a] py-16 lg:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl lg:text-5xl font-bold text-white" style="font-family: 'Playfair Display', serif;">Thanh toán</h1>
            </div>
        </section>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <!-- Form -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Order Type -->
                    <div>
                        <h2 class="text-xs font-semibold tracking-widest uppercase text-[#1a1a1a] mb-4">Hình thức nhận hàng</h2>
                        <div class="flex gap-4">
                            <button
                                v-for="type in order_types"
                                :key="type.value"
                                @click="form.order_type = type.value"
                                :class="form.order_type === type.value ? 'bg-[#1a1a1a] text-white border-[#1a1a1a]' : 'bg-white text-[#1a1a1a] border-gray-300 hover:border-[#1a1a1a]'"
                                class="px-6 py-3 border text-sm font-medium transition flex-1"
                            >
                                {{ type.label }}
                            </button>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div>
                        <h2 class="text-xs font-semibold tracking-widest uppercase text-[#1a1a1a] mb-4">Thông tin người nhận</h2>

                        <div v-if="addresses.length > 0 && form.order_type === 'delivery'" class="mb-6">
                            <p class="text-sm text-gray-500 mb-3">Chọn địa chỉ đã lưu:</p>
                            <div class="space-y-2">
                                <button
                                    v-for="addr in addresses"
                                    :key="addr.id"
                                    @click="selectAddress(addr)"
                                    :class="selectedAddress === addr.id ? 'border-[#1a1a1a] bg-[#f8f5f0]' : 'border-gray-200'"
                                    class="w-full text-left p-4 border transition"
                                >
                                    <p class="font-medium text-sm">
                                        <span v-if="addr.label" class="text-amber-600 mr-1">[{{ addr.label }}]</span>
                                        {{ addr.recipient_name }} — {{ addr.phone }}
                                    </p>
                                    <p class="text-sm text-gray-500 mt-0.5">{{ addr.address_line }}</p>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Tên người nhận *</label>
                                <input v-model="form.customer_name" type="text" class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] py-3 text-sm" />
                                <p v-if="errors.customer_name" class="text-red-500 text-xs mt-1">{{ errors.customer_name[0] }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Số điện thoại *</label>
                                <input v-model="form.customer_phone" type="text" class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] py-3 text-sm" />
                                <p v-if="errors.customer_phone" class="text-red-500 text-xs mt-1">{{ errors.customer_phone[0] }}</p>
                            </div>
                        </div>

                        <div v-if="form.order_type === 'delivery'" class="mt-4">
                            <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Địa chỉ giao hàng *</label>
                            <textarea v-model="form.shipping_address" rows="2" class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] text-sm"></textarea>
                            <p v-if="errors.shipping_address" class="text-red-500 text-xs mt-1">{{ errors.shipping_address[0] }}</p>

                            <div v-if="!selectedAddress" class="mt-3">
                                <label class="flex items-center cursor-pointer">
                                    <input v-model="form.save_address" type="checkbox" class="rounded-sm text-[#1a1a1a] focus:ring-[#1a1a1a] border-gray-400" />
                                    <span class="ml-2 text-sm text-gray-500">Lưu địa chỉ này cho lần sau</span>
                                </label>
                                <div v-if="form.save_address" class="mt-2">
                                    <input v-model="form.address_label" type="text" placeholder="Nhãn địa chỉ (VD: Nhà, Công ty)" class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] text-sm py-2" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Ghi chú</label>
                            <textarea v-model="form.note" rows="2" placeholder="Ví dụ: Giao trước 5h chiều..." class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] text-sm"></textarea>
                        </div>
                    </div>

                    <!-- Payment -->
                    <div>
                        <h2 class="text-xs font-semibold tracking-widest uppercase text-[#1a1a1a] mb-4">Phương thức thanh toán</h2>
                        <div class="space-y-2">
                            <label
                                v-for="method in payment_methods"
                                :key="method.value"
                                class="flex items-center p-4 border cursor-pointer transition"
                                :class="form.payment_method === method.value ? 'border-[#1a1a1a] bg-[#f8f5f0]' : 'border-gray-200'"
                            >
                                <input v-model="form.payment_method" type="radio" :value="method.value" class="text-[#1a1a1a] focus:ring-[#1a1a1a]" />
                                <span class="ml-3 text-sm font-medium">{{ method.label }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div>
                    <div class="bg-[#f8f5f0] p-8 sticky top-24">
                        <h2 class="text-xs font-semibold tracking-widest uppercase text-[#1a1a1a] mb-6">Đơn hàng</h2>
                        <div class="space-y-3 mb-6">
                            <div v-for="item in cart.items" :key="item.id" class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ item.product_name }} x{{ item.quantity }}</span>
                                <span class="font-medium">{{ formatCurrency(item.total) }}</span>
                            </div>
                        </div>

                        <!-- Coupon -->
                        <div class="mb-6">
                            <div class="flex gap-2">
                                <input v-model="form.coupon_code" type="text" placeholder="Mã giảm giá" class="flex-1 border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] text-sm py-2" />
                                <button @click="applyCoupon" class="px-4 py-2 bg-[#1a1a1a] text-white text-xs font-semibold tracking-wider uppercase hover:bg-[#333] transition">Áp dụng</button>
                            </div>
                            <p v-if="couponResult" :class="couponResult.valid ? 'text-green-600' : 'text-red-500'" class="text-xs mt-1.5">{{ couponResult.message }}</p>
                        </div>

                        <!-- Loyalty Points -->
                        <div v-if="loyalty && loyalty.points > 0" class="mb-6 p-4 bg-white border border-gray-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-[#1a1a1a]">
                                    {{ loyalty.tier.tier_icon }} Đổi điểm thưởng
                                </span>
                                <span class="text-[10px] text-gray-500">{{ loyalty.points.toLocaleString() }} điểm</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <input
                                    v-model.number="form.points_used"
                                    type="range"
                                    :min="0"
                                    :max="loyalty.max_redeemable"
                                    :step="1"
                                    class="flex-1 h-1.5 bg-gray-200 rounded appearance-none cursor-pointer accent-[#1a1a1a]"
                                />
                                <span class="text-xs font-semibold min-w-[4rem] text-right">{{ form.points_used }} điểm</span>
                            </div>
                            <p v-if="form.points_used > 0" class="text-xs text-green-600 mt-1">
                                Giảm {{ formatCurrency(pointsDiscount) }}
                            </p>
                            <p class="text-[10px] text-gray-400 mt-1">Tối đa {{ loyalty.max_redeemable }} điểm (30% đơn hàng)</p>
                        </div>

                        <hr class="border-gray-300 my-4" />
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tạm tính</span>
                                <span class="font-medium">{{ formatCurrency(summary.subtotal) }}</span>
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
                                <span class="text-gray-500">Phí giao hàng</span>
                                <span>{{ form.order_type === 'pickup' ? 'Miễn phí' : (summary.shipping_fee === 0 ? 'Miễn phí' : formatCurrency(summary.shipping_fee)) }}</span>
                            </div>
                            <hr class="border-gray-300">
                            <div class="flex justify-between text-base font-bold text-[#1a1a1a]">
                                <span>Tổng cộng</span>
                                <span>{{ formatCurrency(total) }}</span>
                            </div>
                        </div>

                        <p v-if="errors.general" class="text-red-500 text-sm mt-4">{{ errors.general[0] }}</p>

                        <button
                            @click="submit"
                            :disabled="submitting"
                            class="w-full bg-[#1a1a1a] text-white py-3.5 text-sm font-semibold tracking-wider uppercase text-center hover:bg-[#333] transition mt-8 disabled:opacity-50"
                        >
                            {{ submitting ? 'Đang xử lý...' : (form.payment_method === 'payos' ? 'Thanh toán qua PayOS' : 'Đặt hàng') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
