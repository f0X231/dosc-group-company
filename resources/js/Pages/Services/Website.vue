<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>

<template>
    <Head title="รับทำเว็บไซต์" />
    <PublicLayout>
        <!-- Page Header -->
        <section class="bg-gray-50 py-16 text-center">
            <h1 class="text-3xl font-bold text-gray-900">รับทำเว็บไซต์</h1>
            <p class="mt-2 text-gray-500">placeholder</p>
        </section>

        <!-- Packages Pricing -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-4 gap-6">
                <div v-for="pkg in ['One Page', 'Starter', 'Business', 'E-Commerce']" :key="pkg" class="border rounded-xl p-6 text-center">
                    <h3 class="font-bold text-gray-900">{{ pkg }}</h3>
                    <p class="mt-2 text-2xl font-bold text-blue-600">฿ —</p>
                    <p class="mt-4 text-sm text-gray-500">placeholder</p>
                </div>
            </div>
        </section>

        <!-- What's Included -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <h2 class="text-2xl font-bold text-gray-900">ทุกแพ็กเกจคุณจะได้รับ</h2>
                <p class="mt-2 text-gray-500">placeholder</p>
            </div>
        </section>
    </PublicLayout>
</template>
