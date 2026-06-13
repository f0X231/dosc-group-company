<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
    post: { type: Object, required: true },
});
</script>

<template>
    <Head :title="post.title ?? 'บทความ'" />
    <PublicLayout>
        <!-- Article Header -->
        <section class="bg-gray-50 py-16 text-center">
            <div class="max-w-3xl mx-auto px-4">
                <p class="text-sm text-blue-600 font-medium">placeholder — category</p>
                <h1 class="mt-2 text-3xl font-bold text-gray-900">{{ post.title ?? 'ชื่อบทความ' }}</h1>
                <p class="mt-4 text-gray-500 text-sm">placeholder — author · date</p>
            </div>
        </section>

        <!-- Cover Image -->
        <div class="max-w-4xl mx-auto px-4 -mt-4">
            <div class="bg-gray-200 h-64 rounded-xl flex items-center justify-center text-gray-400">
                placeholder — cover image
            </div>
        </div>

        <!-- Article Content -->
        <article class="max-w-3xl mx-auto px-4 py-12">
            <p class="text-gray-400">placeholder — article body</p>
        </article>

        <!-- Related Posts -->
        <section class="py-12 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <h2 class="text-xl font-bold text-gray-900">บทความที่เกี่ยวข้อง</h2>
                <p class="mt-2 text-gray-400">placeholder</p>
            </div>
        </section>
    </PublicLayout>
</template>
