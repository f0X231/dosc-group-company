<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>

<template>
    <Head title="บริการและราคา" />
    <PublicLayout>
        <!-- Page Header -->
        <section class="bg-gray-50 py-16 text-center">
            <h1 class="text-3xl font-bold text-gray-900">บริการและราคา</h1>
            <p class="mt-2 text-gray-500">placeholder</p>
        </section>

        <!-- Service Cards -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-8">
                <div class="border rounded-xl p-8">
                    <h2 class="text-xl font-bold text-gray-900">รับทำเว็บไซต์</h2>
                    <p class="mt-2 text-gray-500">placeholder</p>
                    <Link href="/services/website" class="mt-4 inline-block text-blue-600 font-medium">ดูรายละเอียด &rarr;</Link>
                </div>
                <div class="border rounded-xl p-8">
                    <h2 class="text-xl font-bold text-gray-900">รับทำ Google Ads</h2>
                    <p class="mt-2 text-gray-500">placeholder</p>
                    <Link href="/services/google-ads" class="mt-4 inline-block text-blue-600 font-medium">ดูรายละเอียด &rarr;</Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
