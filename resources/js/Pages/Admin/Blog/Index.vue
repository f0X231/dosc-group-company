<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    posts: { type: Object, default: () => ({}) },
});
</script>

<template>
    <Head title="Admin — จัดการบทความ" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">จัดการบทความ</h1>
            <Link href="/admin/blog/create" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                + เขียนบทความใหม่
            </Link>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">ชื่อบทความ</th>
                        <th class="px-6 py-3 text-left">สถานะ</th>
                        <th class="px-6 py-3 text-left">วันที่เผยแพร่</th>
                        <th class="px-6 py-3 text-left">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t">
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400">placeholder — blog rows</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
