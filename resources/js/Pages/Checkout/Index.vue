<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, watch } from 'vue';
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
    available_coupons: Array,
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

// ── Address Management ────────────────────────────────────
const addressList = ref([...(props.addresses || [])]);
const showAddressModal = ref(false);
const editingAddress = ref(null);
const addressLoading = ref(false);
const addressErrors = ref({});
const addressForm = ref({
    label: '',
    recipient_name: page.props.auth.user?.name || '',
    phone: page.props.auth.user?.phone || '',
    address_line: '',
    is_default: false,
});

const resetAddressForm = () => {
    addressForm.value = {
        label: '',
        recipient_name: page.props.auth.user?.name || '',
        phone: page.props.auth.user?.phone || '',
        address_line: '',
        is_default: false,
    };
    editingAddress.value = null;
    addressErrors.value = {};
};

const openAddAddress = () => {
    resetAddressForm();
    showAddressModal.value = true;
};

const openEditAddress = (addr) => {
    editingAddress.value = addr.id;
    addressForm.value = {
        label: addr.label || '',
        recipient_name: addr.recipient_name,
        phone: addr.phone,
        address_line: addr.address_line,
        is_default: addr.is_default,
    };
    addressErrors.value = {};
    showAddressModal.value = true;
};

const saveAddress = async () => {
    addressLoading.value = true;
    addressErrors.value = {};
    try {
        let res;
        if (editingAddress.value) {
            res = await axios.put(`/api/addresses/${editingAddress.value}`, addressForm.value);
            const idx = addressList.value.findIndex(a => a.id === editingAddress.value);
            if (idx !== -1) {
                // Nếu set default, reset các địa chỉ khác
                if (addressForm.value.is_default) {
                    addressList.value.forEach(a => a.is_default = false);
                }
                addressList.value[idx] = res.data.address;
            }
        } else {
            res = await axios.post('/api/addresses', addressForm.value);
            if (addressForm.value.is_default) {
                addressList.value.forEach(a => a.is_default = false);
            }
            addressList.value.unshift(res.data.address);
        }
        // Sắp xếp lại: default lên trước
        addressList.value.sort((a, b) => (b.is_default ? 1 : 0) - (a.is_default ? 1 : 0));
        showAddressModal.value = false;
        resetAddressForm();
    } catch (e) {
        if (e.response?.status === 422) {
            addressErrors.value = e.response.data.errors || {};
        }
    } finally {
        addressLoading.value = false;
    }
};

const deleteAddress = async (addr) => {
    if (!confirm(`Xóa địa chỉ "${addr.label || addr.address_line}"?`)) return;
    try {
        await axios.delete(`/api/addresses/${addr.id}`);
        addressList.value = addressList.value.filter(a => a.id !== addr.id);
        if (selectedAddress.value === addr.id) {
            selectedAddress.value = null;
            form.value.shipping_address = '';
        }
    } catch (e) {
        alert('Không thể xóa địa chỉ');
    }
};

// ── Select Address ────────────────────────────────────────
const selectAddress = (address) => {
    selectedAddress.value = address.id;
    form.value.shipping_address = address.full_address || address.address_line;
    form.value.customer_name = address.recipient_name;
    form.value.customer_phone = address.phone;
    form.value.save_address = false;
};

const deselectAddress = () => {
    selectedAddress.value = null;
    form.value.shipping_address = '';
    form.value.customer_name = page.props.auth.user?.name || '';
    form.value.customer_phone = page.props.auth.user?.phone || '';
};

// Auto-select default address
const defaultAddr = addressList.value.find(a => a.is_default);
if (defaultAddr) {
    selectAddress(defaultAddr);
}

// ── Coupon & Points ───────────────────────────────────────
const pointsDiscount = computed(() => form.value.points_used * 1000);
const POINTS_VALUE = 1000;

const total = computed(() => {
    const shippingFee = form.value.order_type === 'pickup' ? 0 : Number(props.summary.shipping_fee);
    return Number(props.summary.subtotal) - discount.value - pointsDiscount.value + shippingFee;
});

const applyCoupon = async (code = null) => {
    if (code) form.value.coupon_code = code;
    
    if (!form.value.coupon_code) return;

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

                    <!-- Customer Info & Address -->
                    <div>
                        <h2 class="text-xs font-semibold tracking-widest uppercase text-[#1a1a1a] mb-4">Thông tin người nhận</h2>

                        <!-- ── Address Book Section ─────────────────────── -->
                        <div v-if="form.order_type === 'delivery'" class="mb-6">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-sm font-medium text-gray-700">📍 Sổ địa chỉ</p>
                                <button
                                    @click="openAddAddress"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-[#1a1a1a] text-white hover:bg-[#333] transition"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Thêm địa chỉ
                                </button>
                            </div>

                            <!-- Saved addresses list -->
                            <div v-if="addressList.length > 0" class="space-y-2">
                                <div
                                    v-for="addr in addressList"
                                    :key="addr.id"
                                    :class="selectedAddress === addr.id
                                        ? 'border-[#1a1a1a] bg-[#f8f5f0] ring-1 ring-[#1a1a1a]'
                                        : 'border-gray-200 hover:border-gray-400'"
                                    class="relative p-4 border transition group cursor-pointer"
                                    @click="selectAddress(addr)"
                                >
                                    <!-- Selection indicator -->
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start gap-3 flex-1 min-w-0">
                                            <!-- Radio -->
                                            <div class="mt-0.5 flex-shrink-0">
                                                <div
                                                    :class="selectedAddress === addr.id ? 'border-[#1a1a1a]' : 'border-gray-300'"
                                                    class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition"
                                                >
                                                    <div v-if="selectedAddress === addr.id" class="w-2.5 h-2.5 rounded-full bg-[#1a1a1a]"></div>
                                                </div>
                                            </div>
                                            <!-- Content -->
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium text-sm text-[#1a1a1a]">
                                                    <span v-if="addr.label" class="inline-block bg-amber-100 text-amber-700 text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 mr-1.5">{{ addr.label }}</span>
                                                    <span v-if="addr.is_default" class="inline-block bg-green-100 text-green-700 text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 mr-1.5">Mặc định</span>
                                                    {{ addr.recipient_name }}
                                                </p>
                                                <p class="text-sm text-gray-500 mt-0.5">{{ addr.phone }}</p>
                                                <p class="text-sm text-gray-500 mt-0.5 truncate">{{ addr.address_line }}</p>
                                            </div>
                                        </div>

                                        <!-- Action buttons -->
                                        <div class="flex-shrink-0 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition ml-2">
                                            <button
                                                @click.stop="openEditAddress(addr)"
                                                class="p-1.5 text-gray-400 hover:text-[#1a1a1a] hover:bg-gray-100 rounded transition"
                                                title="Sửa địa chỉ"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button
                                                @click.stop="deleteAddress(addr)"
                                                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition"
                                                title="Xóa địa chỉ"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Deselect / use new address -->
                                <button
                                    v-if="selectedAddress"
                                    @click="deselectAddress"
                                    class="w-full text-center text-xs text-gray-500 hover:text-[#1a1a1a] py-2 transition underline"
                                >
                                    Nhập địa chỉ mới thay vì dùng địa chỉ đã lưu
                                </button>
                            </div>
                            <div v-else class="text-center py-6 border border-dashed border-gray-300">
                                <p class="text-sm text-gray-400 mb-2">Bạn chưa có địa chỉ nào được lưu</p>
                                <button @click="openAddAddress" class="text-sm text-[#D4A853] hover:text-[#1a1a1a] font-medium transition">
                                    + Thêm địa chỉ đầu tiên
                                </button>
                            </div>
                        </div>

                        <!-- Customer fields -->
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
                            <textarea v-model="form.shipping_address" rows="2" class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] text-sm" :placeholder="selectedAddress ? '' : 'Nhập địa chỉ giao hàng...'"></textarea>
                            <p v-if="errors.shipping_address" class="text-red-500 text-xs mt-1">{{ errors.shipping_address[0] }}</p>

                            <!-- Save new address checkbox -->
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
                            <!-- Danh sách mã giảm giá có sẵn -->
                            <div v-if="available_coupons && available_coupons.length > 0" class="mb-3 space-y-2">
                                <span class="text-xs font-semibold text-[#1a1a1a] uppercase tracking-wider block mb-2">Mã giảm giá khả dụng</span>
                                <div 
                                    v-for="coupon in available_coupons" 
                                    :key="coupon.code" 
                                    @click="applyCoupon(coupon.code)"
                                    class="border border-gray-200 rounded-md p-3 cursor-pointer hover:border-[#1a1a1a] hover:bg-gray-50 transition flex items-center justify-between"
                                    :class="{'border-[#1a1a1a] bg-gray-50': form.coupon_code === coupon.code}"
                                >
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-[#1a1a1a] px-1.5 py-0.5 bg-gray-100 rounded tracking-wider">{{ coupon.code }}</span>
                                            <span class="text-xs text-gray-500">{{ coupon.name }}</span>
                                        </div>
                                        <span class="text-xs text-gray-400 mt-1">Đơn tối thiểu: {{ formatCurrency(coupon.min_order_amount) }}</span>
                                    </div>
                                    <button 
                                        type="button" 
                                        class="text-xs px-3 py-1 rounded border border-[#1a1a1a] text-[#1a1a1a] hover:bg-[#1a1a1a] hover:text-white transition font-medium"
                                        :class="{'bg-[#1a1a1a] text-white': form.coupon_code === coupon.code}"
                                    >
                                        {{ form.coupon_code === coupon.code ? 'Đang dùng' : 'Áp dụng' }}
                                    </button>
                                </div>
                            </div>

                            <div class="flex gap-2 mt-2">
                                <input v-model="form.coupon_code" type="text" placeholder="Nhập mã khác" class="flex-1 border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] text-sm py-2 rounded" />
                                <button @click="applyCoupon()" class="px-4 py-2 bg-[#1a1a1a] text-white text-xs font-semibold tracking-wider uppercase hover:bg-[#333] transition rounded">Áp dụng</button>
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

        <!-- ── Address Modal ───────────────────────────────────── -->
        <teleport to="body">
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showAddressModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-black/50" @click="showAddressModal = false"></div>

                    <!-- Modal content -->
                    <div class="relative bg-white w-full max-w-lg shadow-2xl" style="max-height: 90vh; overflow-y: auto;">
                        <!-- Header -->
                        <div class="flex items-center justify-between p-6 border-b">
                            <h3 class="text-lg font-bold text-[#1a1a1a]" style="font-family: 'Playfair Display', serif;">
                                {{ editingAddress ? 'Sửa địa chỉ' : 'Thêm địa chỉ mới' }}
                            </h3>
                            <button @click="showAddressModal = false" class="p-1 text-gray-400 hover:text-[#1a1a1a] transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Nhãn địa chỉ</label>
                                <div class="flex gap-2 flex-wrap">
                                    <button
                                        v-for="lbl in ['Nhà', 'Công ty', 'Khác']"
                                        :key="lbl"
                                        @click="addressForm.label = lbl"
                                        :class="addressForm.label === lbl ? 'bg-[#1a1a1a] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                                        class="px-3 py-1.5 text-xs font-medium transition"
                                        type="button"
                                    >{{ lbl }}</button>
                                </div>
                                <input v-model="addressForm.label" type="text" placeholder="Hoặc nhập tên khác..." class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] text-sm py-2 mt-2" />
                                <p v-if="addressErrors.label" class="text-red-500 text-xs mt-1">{{ addressErrors.label[0] }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Tên người nhận *</label>
                                    <input v-model="addressForm.recipient_name" type="text" class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] py-2.5 text-sm" />
                                    <p v-if="addressErrors.recipient_name" class="text-red-500 text-xs mt-1">{{ addressErrors.recipient_name[0] }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Số điện thoại *</label>
                                    <input v-model="addressForm.phone" type="text" class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] py-2.5 text-sm" />
                                    <p v-if="addressErrors.phone" class="text-red-500 text-xs mt-1">{{ addressErrors.phone[0] }}</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Địa chỉ chi tiết *</label>
                                <textarea v-model="addressForm.address_line" rows="3" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố" class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] text-sm"></textarea>
                                <p v-if="addressErrors.address_line" class="text-red-500 text-xs mt-1">{{ addressErrors.address_line[0] }}</p>
                            </div>

                            <div>
                                <label class="flex items-center cursor-pointer">
                                    <input v-model="addressForm.is_default" type="checkbox" class="rounded-sm text-[#1a1a1a] focus:ring-[#1a1a1a] border-gray-400" />
                                    <span class="ml-2 text-sm text-gray-600">Đặt làm địa chỉ mặc định</span>
                                </label>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="flex items-center justify-end gap-3 p-6 border-t bg-gray-50">
                            <button
                                @click="showAddressModal = false"
                                class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-[#1a1a1a] transition"
                                type="button"
                            >Hủy</button>
                            <button
                                @click="saveAddress"
                                :disabled="addressLoading"
                                class="px-6 py-2.5 bg-[#1a1a1a] text-white text-sm font-semibold tracking-wider uppercase hover:bg-[#333] transition disabled:opacity-50"
                                type="button"
                            >
                                {{ addressLoading ? 'Đang lưu...' : (editingAddress ? 'Cập nhật' : 'Thêm địa chỉ') }}
                            </button>
                        </div>
                    </div>
                </div>
            </transition>
        </teleport>
    </AppLayout>
</template>
