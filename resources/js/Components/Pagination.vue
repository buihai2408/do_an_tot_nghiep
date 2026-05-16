<template>
    <div v-if="links.length > 3" class="flex flex-wrap -mb-1 mt-6 justify-center">
        <template v-for="(link, key) in links" :key="key">
            <div
                v-if="link.url === null"
                class="mr-1 mb-1 px-4 py-2 text-sm leading-4 text-gray-400 border rounded"
                v-html="link.label"
            ></div>
            <Link
                v-else
                class="mr-1 mb-1 px-4 py-2 text-sm leading-4 border rounded hover:bg-[#FAF6F0] focus:border-indigo-500 focus:text-indigo-500 transition"
                :class="{ 'bg-[#D4A853] text-white border-[#D4A853] hover:bg-[#c29641]': link.active, 'text-gray-700 border-gray-300': !link.active }"
                :href="link.url"
                v-html="link.label"
            />
        </template>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: Array,
});
</script>
