<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    contacts: { type: Object, default: () => ({}) },
});
</script>

<template>
    <Head title="Admin — ข้อความติดต่อ" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">ข้อความติดต่อ</h1>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">ชื่อ</th>
                        <th class="px-6 py-3 text-left">เบอร์โทร / LINE</th>
                        <th class="px-6 py-3 text-left">ข้อความ</th>
                        <th class="px-6 py-3 text-left">วันที่</th>
                        <th class="px-6 py-3 text-left">สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t">
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">placeholder — contact rows</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
