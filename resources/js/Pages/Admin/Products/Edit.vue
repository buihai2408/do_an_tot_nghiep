<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({ product: Object, categories: Array, sizes: Array, toppings: Array });

const form = ref({
    category_id: props.product.category_id,
    name: props.product.name,
    description: props.product.description || '',
    base_price: props.product.base_price,
    is_active: props.product.is_active,
    is_featured: props.product.is_featured,
});
const newImageFiles = ref([]);
const newPreviews = ref([]);
const existingImages = ref([...(props.product.images || [])]);
const deleteImageIds = ref([]);
const primaryImageId = ref(props.product.images?.find(i => i.is_primary)?.id || props.product.images?.[0]?.id || null);
const selectedSizes = ref(props.sizes.map(s => {
    const existing = props.product.sizes?.find(ps => ps.id === s.id);
    return { size_id: s.id, name: s.name, price: existing?.pivot?.price || '', enabled: !!existing };
}));
const selectedToppings = ref(props.product.toppings?.map(t => t.id) || []);
const errors = ref({});
const submitting = ref(false);

const handleImageSelect = (e) => {
    const files = Array.from(e.target.files);
    files.forEach(file => {
        newImageFiles.value.push(file);
        const reader = new FileReader();
        reader.onload = (ev) => newPreviews.value.push({ url: ev.target.result, name: file.name });
        reader.readAsDataURL(file);
    });
    e.target.value = '';
};

const removeNewImage = (index) => {
    newImageFiles.value.splice(index, 1);
    newPreviews.value.splice(index, 1);
};

const removeExistingImage = (img) => {
    deleteImageIds.value.push(img.id);
    existingImages.value = existingImages.value.filter(i => i.id !== img.id);
    if (primaryImageId.value === img.id) {
        primaryImageId.value = existingImages.value[0]?.id || null;
    }
};

const setPrimary = (id) => {
    primaryImageId.value = id;
};

const submit = async () => {
    submitting.value = true;
    const formData = new FormData();
    Object.entries(form.value).forEach(([k, v]) => formData.append(k, v === true ? '1' : v === false ? '0' : v ?? ''));

    newImageFiles.value.forEach((file, i) => formData.append(`images[${i}]`, file));

    if (deleteImageIds.value.length) {
        formData.append('delete_images', JSON.stringify(deleteImageIds.value));
    }
    if (primaryImageId.value) {
        formData.append('primary_image_id', primaryImageId.value);
    }

    const enabledSizes = selectedSizes.value.filter(s => s.enabled);
    enabledSizes.forEach((s, i) => {
        formData.append(`sizes[${i}][size_id]`, s.size_id);
        formData.append(`sizes[${i}][price]`, s.price || form.value.base_price);
    });

    selectedToppings.value.forEach((id, i) => formData.append(`topping_ids[${i}]`, id));

    try {
        await axios.post(`/api/admin/products/${props.product.id}`, formData);
        router.visit('/admin/products');
    } catch (e) {
        errors.value = e.response?.data?.errors || {};
    } finally {
        submitting.value = false;
    }
};
</script>

<template>
    <AdminLayout>
        <h1 class="text-2xl font-bold text-[#1a1a1a] font-serif mb-6">Sửa sản phẩm: {{ product.name }}</h1>

        <div class="bg-white rounded border border-[#E8D9C5] p-6 shadow-sm max-w-3xl">
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium mb-1">Tên *</label><input v-model="form.name" class="w-full rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]" /></div>
                    <div><label class="block text-sm font-medium mb-1">Danh mục *</label>
                        <select v-model="form.category_id" class="w-full rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]">
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                    </div>
                </div>
                <div><label class="block text-sm font-medium mb-1">Mô tả</label><textarea v-model="form.description" rows="3" class="w-full rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]"></textarea></div>
                <div>
                    <label class="block text-sm font-medium mb-1">Giá cơ bản *</label>
                    <input v-model="form.base_price" type="number" class="w-full rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853] max-w-xs" />
                </div>

                <!-- Existing Images -->
                <div>
                    <label class="block text-sm font-medium mb-2">Hình ảnh sản phẩm</label>
                    <div v-if="existingImages.length" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3 mb-3">
                        <div
                            v-for="img in existingImages"
                            :key="img.id"
                            class="relative group rounded-lg overflow-hidden border-2 cursor-pointer"
                            :class="primaryImageId === img.id ? 'border-amber-500' : 'border-gray-200'"
                            @click="setPrimary(img.id)"
                        >
                            <img :src="`/storage/${img.path}`" class="w-full h-24 object-cover" />
                            <span v-if="primaryImageId === img.id" class="absolute top-1 left-1 bg-amber-500 text-white text-[10px] px-1.5 py-0.5 rounded font-semibold">Chính</span>
                            <button
                                @click.stop="removeExistingImage(img)"
                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition"
                            >&times;</button>
                        </div>
                    </div>

                    <!-- New Image Previews -->
                    <div v-if="newPreviews.length" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3 mb-3">
                        <div v-for="(preview, index) in newPreviews" :key="'new-'+index" class="relative group rounded-lg overflow-hidden border-2 border-dashed border-green-400">
                            <img :src="preview.url" class="w-full h-24 object-cover" />
                            <span class="absolute top-1 left-1 bg-green-500 text-white text-[10px] px-1.5 py-0.5 rounded font-semibold">Mới</span>
                            <button
                                @click="removeNewImage(index)"
                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition"
                            >&times;</button>
                        </div>
                    </div>

                    <label class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg cursor-pointer transition text-sm">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Thêm ảnh
                        <input type="file" multiple accept="image/*" @change="handleImageSelect" class="hidden" />
                    </label>
                    <p class="text-xs text-gray-400 mt-1">Click vào ảnh để đặt làm ảnh chính. Tối đa 10 ảnh, mỗi ảnh ≤ 2MB.</p>
                </div>

                <div class="flex space-x-6">
                    <label class="flex items-center"><input v-model="form.is_active" type="checkbox" class="rounded text-[#D4A853] mr-2" /> Đang bán</label>
                    <label class="flex items-center"><input v-model="form.is_featured" type="checkbox" class="rounded text-[#D4A853] mr-2" /> Nổi bật</label>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Kích thước & Giá</label>
                    <div v-for="size in selectedSizes" :key="size.size_id" class="flex items-center space-x-4 mb-2">
                        <label class="flex items-center"><input v-model="size.enabled" type="checkbox" class="rounded text-[#D4A853] mr-2" /> {{ size.name }}</label>
                        <input v-if="size.enabled" v-model="size.price" type="number" class="flex-1 rounded border-[#E8D9C5] focus:border-[#D4A853] focus:ring-[#D4A853]" />
                    </div>
                </div>
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
                    {{ submitting ? 'Đang cập nhật...' : 'Cập nhật' }}
                </button>
            </div>
        </div>
    </AdminLayout>
</template>
