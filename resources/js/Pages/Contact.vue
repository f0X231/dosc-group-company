<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>

<template>
    <Head title="ติดต่อเรา" />
    <PublicLayout>
        <!-- Page Header -->
        <section class="bg-gray-50 py-16 text-center">
            <h1 class="text-3xl font-bold text-gray-900">ติดต่อเรา</h1>
            <p class="mt-2 text-gray-500">placeholder</p>
        </section>

        <!-- Contact Content -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-12">
                <!-- Contact Info -->
                <div>
                    <h2 class="text-xl font-bold text-gray-900">ช่องทางติดต่อ</h2>
                    <p class="mt-4 text-gray-500">placeholder — phone, LINE, email, address</p>
                </div>

                <!-- Contact Form -->
                <div>
                    <h2 class="text-xl font-bold text-gray-900">ส่งข้อความหาเรา</h2>
                    <p class="mt-4 text-gray-400">placeholder — contact form</p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
