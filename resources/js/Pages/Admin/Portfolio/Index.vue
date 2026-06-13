<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    portfolios: { type: Object, default: () => ({}) },
});
</script>

<template>
    <Head title="Admin — จัดการผลงาน" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">จัดการผลงาน</h1>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                + เพิ่มผลงาน
            </button>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-4 flex gap-3">
            <select class="border rounded-md px-3 py-1.5 text-sm text-gray-700">
                <option>ทุกแพ็กเกจ</option>
                <option>One Page</option>
                <option>Starter</option>
                <option>Business</option>
                <option>E-Commerce</option>
            </select>
            <input type="text" placeholder="ค้นหา..." class="border rounded-md px-3 py-1.5 text-sm flex-1" />
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">ชื่อโปรเจค</th>
                        <th class="px-6 py-3 text-left">แพ็กเกจ</th>
                        <th class="px-6 py-3 text-left">วันที่สร้าง</th>
                        <th class="px-6 py-3 text-left">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t">
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400">placeholder — portfolio rows</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
