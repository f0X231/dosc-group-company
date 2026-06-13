<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    stats: { type: Object, default: () => ({}) },
});
</script>

<template>
    <Head title="Admin — Dashboard" />
    <AdminLayout>
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Dashboard</h1>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div v-for="label in ['ผลงานทั้งหมด', 'บทความ', 'ข้อความใหม่', 'ผู้เข้าชม']" :key="label"
                class="bg-white rounded-xl shadow-sm p-6">
                <p class="text-sm text-gray-500">{{ label }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">—</p>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">กิจกรรมล่าสุด</h2>
            <p class="text-gray-400">placeholder</p>
        </div>
    </AdminLayout>
</template>
