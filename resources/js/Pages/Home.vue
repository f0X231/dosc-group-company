<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>

<template>
    <Head title="หน้าแรก" />
    <PublicLayout>
        <!-- Hero Section -->
        <section class="bg-gray-50 py-24 text-center">
            <h1 class="text-4xl font-bold text-gray-900">หน้าแรก — Hero Section</h1>
            <p class="mt-4 text-gray-500">placeholder</p>
        </section>

        <!-- Packages Overview -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <h2 class="text-2xl font-bold text-gray-900">แพ็กเกจของเรา</h2>
                <p class="mt-2 text-gray-500">placeholder</p>
            </div>
        </section>

        <!-- Why Us -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <h2 class="text-2xl font-bold text-gray-900">ทำไมต้องเลือกเรา</h2>
                <p class="mt-2 text-gray-500">placeholder</p>
            </div>
        </section>

        <!-- Process -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <h2 class="text-2xl font-bold text-gray-900">ขั้นตอนการทำงาน</h2>
                <p class="mt-2 text-gray-500">placeholder</p>
            </div>
        </section>

        <!-- Portfolio Preview -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <h2 class="text-2xl font-bold text-gray-900">ผลงานที่ผ่านมา</h2>
                <p class="mt-2 text-gray-500">placeholder</p>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <h2 class="text-2xl font-bold text-gray-900">รีวิวจากลูกค้า</h2>
                <p class="mt-2 text-gray-500">placeholder</p>
            </div>
        </section>

        <!-- Blog Preview -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <h2 class="text-2xl font-bold text-gray-900">บทความล่าสุด</h2>
                <p class="mt-2 text-gray-500">placeholder</p>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-16 bg-blue-600 text-white text-center">
            <h2 class="text-2xl font-bold">ปรึกษาฟรี คลิกเลย</h2>
            <p class="mt-2 text-blue-100">placeholder</p>
        </section>
    </PublicLayout>
</template>
