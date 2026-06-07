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
const generatingDesc = ref(false);

const generateDescription = async () => {
    if (!form.value.name) {
        alert('Vui lòng nhập tên sản phẩm trước khi tạo mô tả AI.');
        return;
    }
    generatingDesc.value = true;
    try {
        const categoryName = props.categories.find(c => c.id == form.value.category_id)?.name || '';
        const { data } = await axios.post('/api/admin/ai/generate-description', {
            name: form.value.name,
            category: categoryName,
        });
        if (data.description) {
            form.value.description = data.description;
        }
    } catch (e) {
        const msg = e.response?.data?.error || 'Không thể tạo mô tả AI. Vui lòng thử lại.';
        alert(msg);
    } finally {
        generatingDesc.value = false;
    }
};

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
                        <input v-model="form.name" class="w-full rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]" />
                        <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Danh mục *</label>
                        <select v-model="form.category_id" class="w-full rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]">
                            <option value="">Chọn danh mục</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Mô tả</label>
                    <div class="relative group">
                        <textarea
                            v-model="form.description"
                            rows="4"
                            class="w-full rounded-xl border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853] transition-all duration-300 pb-12 shadow-sm"
                            :class="generatingDesc ? 'ring-2 ring-purple-400 border-purple-400 bg-purple-50/30' : ''"
                            :placeholder="generatingDesc ? 'AI đang viết mô tả...' : 'Nhập mô tả món nước hấp dẫn hoặc dùng AI hỗ trợ...'"
                        ></textarea>
                        
                        <div class="absolute bottom-3 right-3 flex items-center opacity-80 group-focus-within:opacity-100 hover:opacity-100 transition-opacity">
                            <button
                                type="button"
                                @click="generateDescription"
                                :disabled="generatingDesc"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all duration-300"
                                :class="generatingDesc
                                    ? 'bg-purple-100 text-purple-400 cursor-wait'
                                    : 'bg-gradient-to-r from-purple-500 to-indigo-500 text-white hover:from-purple-600 hover:to-indigo-600 shadow-md transform hover:-translate-y-0.5'"
                            >
                                <svg v-if="!generatingDesc" class="w-4 h-4 animate-pulse" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L9.19 8.63 2 9.24l5.46 4.73L5.82 21 12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61z"/>
                                </svg>
                                <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                {{ generatingDesc ? 'Đang viết...' : (form.description ? '✨ Viết lại' : '✨ AI Viết') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Giá cơ bản *</label>
                    <input v-model="form.base_price" type="number" class="w-full rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853] max-w-xs" />
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
