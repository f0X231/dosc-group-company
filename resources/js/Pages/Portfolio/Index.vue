<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
    portfolios: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="ผลงาน" />
    <PublicLayout>
        <!-- Page Header -->
        <section class="bg-gray-50 py-16 text-center">
            <h1 class="text-3xl font-bold text-gray-900">ผลงานของเรา</h1>
            <p class="mt-2 text-gray-500">placeholder</p>
        </section>

        <!-- Filter Tabs -->
        <section class="py-6 bg-white border-b">
            <div class="max-w-7xl mx-auto px-4 flex gap-4 justify-center">
                <button class="px-4 py-2 rounded-full bg-blue-600 text-white text-sm">ทั้งหมด</button>
                <button class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm">One Page</button>
                <button class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm">Starter</button>
                <button class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm">Business</button>
                <button class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm">E-Commerce</button>
            </div>
        </section>

        <!-- Portfolio Grid -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4">
                <p class="text-center text-gray-400">placeholder — portfolio grid</p>
            </div>
        </section>
    </PublicLayout>
</template>
