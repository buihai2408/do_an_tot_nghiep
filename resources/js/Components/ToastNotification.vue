<script setup>
import { useToast } from '@/Composables/useToast';

const { toasts } = useToast();

const getToastClass = (type) => {
    switch (type) {
        case 'success': return 'bg-green-100 border-green-500 text-green-800';
        case 'error': return 'bg-red-100 border-red-500 text-red-800';
        case 'info': return 'bg-blue-100 border-blue-500 text-blue-800';
        default: return 'bg-gray-100 border-gray-500 text-gray-800';
    }
};

const getIcon = (type) => {
    switch (type) {
        case 'success': return '✓';
        case 'error': return '✕';
        case 'info': return 'ℹ';
        default: return '•';
    }
};
</script>

<template>
    <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none">
        <TransitionGroup
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="transform translate-x-full opacity-0"
            enter-to-class="transform translate-x-0 opacity-100"
            leave-active-class="transition duration-200 ease-in absolute w-full"
            leave-from-class="transform translate-x-0 opacity-100"
            leave-to-class="transform translate-x-full opacity-0"
            move-class="transition duration-300"
        >
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="min-w-[250px] max-w-sm px-4 py-3 border-l-4 rounded shadow-lg flex items-center gap-3 bg-white"
                :class="getToastClass(toast.type)"
            >
                <div class="font-bold flex-shrink-0" aria-hidden="true">{{ getIcon(toast.type) }}</div>
                <div class="text-sm font-medium">{{ toast.message }}</div>
            </div>
        </TransitionGroup>
    </div>
</template>
