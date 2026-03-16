import { ref } from 'vue';

const toasts = ref([]);
let nextId = 0;

export function useToast() {
    const addToast = (message, type = 'success', duration = 3000) => {
        const id = nextId++;
        toasts.value.push({ id, message, type });
        setTimeout(() => {
            toasts.value = toasts.value.filter(t => t.id !== id);
        }, duration);
    };

    const success = (message) => addToast(message, 'success');
    const errorToast = (message) => addToast(message, 'error');
    const info = (message) => addToast(message, 'info');

    return { toasts, success, error: errorToast, info };
}
