<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
    faqs: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="คำถามที่พบบ่อย" />
    <PublicLayout>
        <!-- Page Header -->
        <section class="bg-gray-50 py-16 text-center">
            <h1 class="text-3xl font-bold text-gray-900">คำถามที่พบบ่อย</h1>
            <p class="mt-2 text-gray-500">placeholder</p>
        </section>

        <!-- FAQ List -->
        <section class="py-16 bg-white">
            <div class="max-w-3xl mx-auto px-4">
                <p class="text-center text-gray-400">placeholder — accordion FAQ</p>
            </div>
        </section>
    </PublicLayout>
</template>
