import { ref } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

const loading = ref(false);
const error = ref(null);

export function useCart() {
    const addToCart = async (data) => {
        loading.value = true;
        error.value = null;
        try {
            const response = await axios.post('/api/cart/items', data);
            router.reload({ only: ['cart_count'] });
            return response.data;
        } catch (e) {
            error.value = e.response?.data?.message || 'Có lỗi xảy ra';
            throw e;
        } finally {
            loading.value = false;
        }
    };

    const updateItem = async (itemId, quantity) => {
        loading.value = true;
        try {
            const response = await axios.put(`/api/cart/items/${itemId}`, { quantity });
            router.reload();
            return response.data;
        } catch (e) {
            error.value = e.response?.data?.message || 'Có lỗi xảy ra';
            throw e;
        } finally {
            loading.value = false;
        }
    };

    const removeItem = async (itemId) => {
        loading.value = true;
        try {
            const response = await axios.delete(`/api/cart/items/${itemId}`);
            router.reload();
            return response.data;
        } catch (e) {
            error.value = e.response?.data?.message || 'Có lỗi xảy ra';
            throw e;
        } finally {
            loading.value = false;
        }
    };

    const clearCart = async () => {
        loading.value = true;
        try {
            const response = await axios.delete('/api/cart');
            router.reload();
            return response.data;
        } catch (e) {
            error.value = e.response?.data?.message || 'Có lỗi xảy ra';
            throw e;
        } finally {
            loading.value = false;
        }
    };

    return { addToCart, updateItem, removeItem, clearCart, loading, error };
}
