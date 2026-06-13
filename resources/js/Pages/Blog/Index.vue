<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
    posts: { type: Object, default: () => ({}) },
});
</script>

<template>
    <Head title="บทความ" />
    <PublicLayout>
        <!-- Page Header -->
        <section class="bg-gray-50 py-16 text-center">
            <h1 class="text-3xl font-bold text-gray-900">บทความ</h1>
            <p class="mt-2 text-gray-500">placeholder</p>
        </section>

        <!-- Blog Grid -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4">
                <p class="text-center text-gray-400">placeholder — blog card grid</p>
            </div>
        </section>

        <!-- Pagination -->
        <div class="py-8 flex justify-center">
            <p class="text-gray-400 text-sm">placeholder — pagination</p>
        </div>
    </PublicLayout>
</template>
