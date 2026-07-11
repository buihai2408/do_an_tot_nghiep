<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const page = usePage();
const user = page.props.auth.user;

const profileForm = useForm({
    name: user.name,
    phone: user.phone || '',
    email: user.email,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const profileSaved = ref(false);
const passwordSaved = ref(false);
const showDeleteModal = ref(false);
const deleteForm = useForm({ password: '' });

const updateProfile = () => {
    profileForm.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            profileSaved.value = true;
            setTimeout(() => profileSaved.value = false, 3000);
        },
    });
};

const updatePassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
            passwordSaved.value = true;
            setTimeout(() => passwordSaved.value = false, 3000);
        },
    });
};

const deleteAccount = () => {
    deleteForm.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => showDeleteModal.value = false,
    });
};
</script>

<template>
    <AppLayout>
        
        <section class="bg-[#1a1a1a] py-16 lg:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl lg:text-5xl font-bold text-white" style="font-family: 'Playfair Display', serif;">Hồ sơ cá nhân</h1>
            </div>
        </section>

        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            
            <div class="bg-white border border-gray-100 p-8 mb-8">
                <h2 class="text-xs font-semibold tracking-widest uppercase text-[#1a1a1a] mb-1">Thông tin cá nhân</h2>
                <p class="text-sm text-gray-400 mb-6">Cập nhật tên và email của bạn.</p>

                <form @submit.prevent="updateProfile" class="space-y-5">
                    <div>
                        <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Họ tên</label>
                        <input v-model="profileForm.name" type="text" class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] py-3 text-sm" />
                        <p v-if="profileForm.errors.name" class="text-red-500 text-xs mt-1">{{ profileForm.errors.name }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Số điện thoại</label>
                        <input v-model="profileForm.phone" type="tel" class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] py-3 text-sm" placeholder="0966461728" />
                        <p v-if="profileForm.errors.phone" class="text-red-500 text-xs mt-1">{{ profileForm.errors.phone }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Email</label>
                        <input v-model="profileForm.email" type="email" class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] py-3 text-sm" />
                        <p v-if="profileForm.errors.email" class="text-red-500 text-xs mt-1">{{ profileForm.errors.email }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="submit" :disabled="profileForm.processing" class="px-6 py-2.5 bg-[#1a1a1a] text-white text-sm font-semibold tracking-wider uppercase hover:bg-[#333] transition disabled:opacity-50">
                            Lưu thay đổi
                        </button>
                        <span v-if="profileSaved" class="text-sm text-green-600 font-medium">Đã lưu!</span>
                    </div>
                </form>
            </div>

            
            <div class="bg-white border border-gray-100 p-8 mb-8">
                <h2 class="text-xs font-semibold tracking-widest uppercase text-[#1a1a1a] mb-1">Đổi mật khẩu</h2>
                <p class="text-sm text-gray-400 mb-6">Đảm bảo tài khoản của bạn sử dụng mật khẩu mạnh.</p>

                <form @submit.prevent="updatePassword" class="space-y-5">
                    <div>
                        <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Mật khẩu hiện tại</label>
                        <input v-model="passwordForm.current_password" type="password" class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] py-3 text-sm" />
                        <p v-if="passwordForm.errors.current_password" class="text-red-500 text-xs mt-1">{{ passwordForm.errors.current_password }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Mật khẩu mới</label>
                        <input v-model="passwordForm.password" type="password" class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] py-3 text-sm" />
                        <p v-if="passwordForm.errors.password" class="text-red-500 text-xs mt-1">{{ passwordForm.errors.password }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold tracking-widest uppercase text-gray-500 mb-2">Xác nhận mật khẩu mới</label>
                        <input v-model="passwordForm.password_confirmation" type="password" class="w-full border-gray-300 focus:border-[#1a1a1a] focus:ring-[#1a1a1a] py-3 text-sm" />
                        <p v-if="passwordForm.errors.password_confirmation" class="text-red-500 text-xs mt-1">{{ passwordForm.errors.password_confirmation }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="submit" :disabled="passwordForm.processing" class="px-6 py-2.5 bg-[#1a1a1a] text-white text-sm font-semibold tracking-wider uppercase hover:bg-[#333] transition disabled:opacity-50">
                            Cập nhật mật khẩu
                        </button>
                        <span v-if="passwordSaved" class="text-sm text-green-600 font-medium">Đã cập nhật!</span>
                    </div>
                </form>
            </div>

            
            <div class="bg-white border border-red-200 p-8">
                <h2 class="text-xs font-semibold tracking-widest uppercase text-red-600 mb-1">Xóa tài khoản</h2>
                <p class="text-sm text-gray-400 mb-4">Sau khi xóa, toàn bộ dữ liệu của bạn sẽ bị mất vĩnh viễn.</p>
                <button @click="showDeleteModal = true" class="px-5 py-2.5 bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition">
                    Xóa tài khoản
                </button>
            </div>

            
            <Teleport to="body">
                <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center">
                    <div class="fixed inset-0 bg-black/50" @click="showDeleteModal = false"></div>
                    <div class="relative bg-white shadow-xl p-8 w-full max-w-md mx-4">
                        <h3 class="text-lg font-bold text-[#1a1a1a] mb-2" style="font-family: 'Playfair Display', serif;">Bạn chắc chắn muốn xóa tài khoản?</h3>
                        <p class="text-sm text-gray-500 mb-4">Nhập mật khẩu để xác nhận. Thao tác này không thể hoàn tác.</p>
                        <input
                            v-model="deleteForm.password"
                            type="password"
                            placeholder="Mật khẩu"
                            class="w-full border-gray-300 focus:border-red-500 focus:ring-red-500 py-3 text-sm mb-2"
                            @keyup.enter="deleteAccount"
                        />
                        <p v-if="deleteForm.errors.password" class="text-red-500 text-xs mb-3">{{ deleteForm.errors.password }}</p>
                        <div class="flex justify-end gap-3 mt-4">
                            <button @click="showDeleteModal = false; deleteForm.reset()" class="px-4 py-2.5 text-sm text-gray-600 hover:text-[#1a1a1a] transition">Hủy</button>
                            <button @click="deleteAccount" :disabled="deleteForm.processing" class="px-5 py-2.5 bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition disabled:opacity-50">
                                Xóa tài khoản
                            </button>
                        </div>
                    </div>
                </div>
            </Teleport>
        </div>
    </AppLayout>
</template>
