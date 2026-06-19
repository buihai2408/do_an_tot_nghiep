<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useFormatters } from '@/Composables/useFormatters';
import axios from 'axios';

const { formatCurrency } = useFormatters();

const props = defineProps({ categories: Array, sizes: Array, toppings: Array });

const form = ref({
    category_id: '', name: '', description: '', base_price: '',
    is_active: true, is_featured: false,
    has_ice_level: true, has_sugar_level: true,
});
const imageFiles = ref([]);
const imagePreviews = ref([]);
const selectedSizes = ref(props.sizes.map(s => ({ size_id: s.id, name: s.name, price: '', enabled: false })));
const selectedToppings = ref([]);
const errors = ref({});
const generalError = ref('');
const submitting = ref(false);

const handleImageSelect = (e) => {
    const files = Array.from(e.target.files);
    files.forEach(file => {
        imageFiles.value.push(file);
        const reader = new FileReader();
        reader.onload = (ev) => imagePreviews.value.push({ url: ev.target.result, name: file.name });
        reader.readAsDataURL(file);
    });
    e.target.value = '';
};

const removeImage = (index) => {
    imageFiles.value.splice(index, 1);
    imagePreviews.value.splice(index, 1);
};

const submit = async () => {
    submitting.value = true;
    const formData = new FormData();
    Object.entries(form.value).forEach(([k, v]) => formData.append(k, v === true ? '1' : v === false ? '0' : v ?? ''));

    imageFiles.value.forEach((file, i) => formData.append(`images[${i}]`, file));

    const enabledSizes = selectedSizes.value.filter(s => s.enabled);
    enabledSizes.forEach((s, i) => {
        formData.append(`sizes[${i}][size_id]`, s.size_id);
        formData.append(`sizes[${i}][price]`, s.price || form.value.base_price);
    });

    selectedToppings.value.forEach((id, i) => formData.append(`topping_ids[${i}]`, id));

    try {
        generalError.value = '';
        errors.value = {};
        await axios.post('/api/admin/products', formData);
        router.visit('/admin/products');
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors || {};
        } else {
            generalError.value = e.response?.data?.message || 'Có lỗi xảy ra, vui lòng thử lại.';
        }
    } finally {
        submitting.value = false;
    }
};
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-bold text-[#1a1a1a] font-serif mb-6">Thêm sản phẩm</h1>

        <div class="bg-white rounded border border-[#E8D9C5] p-6 shadow-sm max-w-3xl">
            <div v-if="generalError" class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ generalError }}</div>
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Tên sản phẩm *</label>
                        <input v-model="form.name" :class="errors.name ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]'" class="w-full rounded" />
                        <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Danh mục *</label>
                        <select v-model="form.category_id" :class="errors.category_id ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]'" class="w-full rounded">
                            <option value="">Chọn danh mục</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                        <p v-if="errors.category_id" class="text-red-500 text-sm mt-1">{{ errors.category_id[0] }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Mô tả</label>
                    <textarea
                        v-model="form.description"
                        rows="4"
                        class="w-full rounded-xl border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853] transition-all duration-300 shadow-sm"
                        placeholder="Nhập mô tả sản phẩm..."
                    ></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Giá cơ bản *</label>
                    <input v-model="form.base_price" type="number" :class="errors.base_price ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]'" class="w-full rounded max-w-xs" />
                    <p v-if="errors.base_price" class="text-red-500 text-sm mt-1">{{ errors.base_price[0] }}</p>
                </div>

                <!-- Images Upload -->
                <div>
                    <label class="block text-sm font-medium mb-2">Hình ảnh sản phẩm</label>
                    <div v-if="imagePreviews.length" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3 mb-3">
                        <div
                            v-for="(preview, index) in imagePreviews"
                            :key="index"
                            class="relative group rounded-lg overflow-hidden border-2"
                            :class="index === 0 ? 'border-amber-500' : 'border-gray-200'"
                        >
                            <img :src="preview.url" class="w-full h-24 object-cover" />
                            <span v-if="index === 0" class="absolute top-1 left-1 bg-amber-500 text-white text-[10px] px-1.5 py-0.5 rounded font-semibold">Chính</span>
                            <button
                                @click="removeImage(index)"
                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition"
                            >&times;</button>
                        </div>
                    </div>
                    <label class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg cursor-pointer transition text-sm">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Chọn ảnh
                        <input type="file" multiple accept="image/*" @change="handleImageSelect" class="hidden" />
                    </label>
                    <p class="text-xs text-gray-400 mt-1">Ảnh đầu tiên sẽ là ảnh chính. Tối đa 10 ảnh, mỗi ảnh ≤ 2MB.</p>
                    <p v-if="errors.images" class="text-red-500 text-sm mt-1">{{ errors.images[0] }}</p>
                </div>

                <div class="flex flex-wrap gap-6">
                    <label class="flex items-center"><input v-model="form.is_active" type="checkbox" class="rounded text-[#D4A853] mr-2" /> Đang bán</label>
                    <label class="flex items-center"><input v-model="form.is_featured" type="checkbox" class="rounded text-[#D4A853] mr-2" /> Nổi bật</label>
                    <label class="flex items-center"><input v-model="form.has_ice_level" type="checkbox" class="rounded text-[#D4A853] mr-2" /> Chọn Mức đá</label>
                    <label class="flex items-center"><input v-model="form.has_sugar_level" type="checkbox" class="rounded text-[#D4A853] mr-2" /> Chọn Mức đường</label>
                </div>

                <!-- Sizes -->
                <div>
                    <label class="block text-sm font-medium mb-2">Kích thước & Giá</label>
                    <div v-for="size in selectedSizes" :key="size.size_id" class="flex items-center space-x-4 mb-2">
                        <label class="flex items-center"><input v-model="size.enabled" type="checkbox" class="rounded text-[#D4A853] mr-2" /> {{ size.name }}</label>
                        <input v-if="size.enabled" v-model="size.price" type="number" :placeholder="`Giá size ${size.name}`" class="flex-1 rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]" />
                    </div>
                </div>

                <!-- Toppings -->
                <div>
                    <label class="block text-sm font-medium mb-2">Topping</label>
                    <div class="flex flex-wrap gap-2">
                        <label v-for="topping in toppings" :key="topping.id" class="flex items-center">
                            <input v-model="selectedToppings" :value="topping.id" type="checkbox" class="rounded text-[#D4A853] mr-1" />
                            <span class="text-sm">{{ topping.name }}</span>
                        </label>
                    </div>
                </div>

                <button @click="submit" :disabled="submitting" class="px-6 py-3 bg-amber-700 text-white rounded-xl font-semibold hover:bg-amber-600 transition disabled:opacity-50">
                    {{ submitting ? 'Đang tạo...' : 'Tạo sản phẩm' }}
                </button>
            </div>
        </div>
    </AdminLayout>
</template>
