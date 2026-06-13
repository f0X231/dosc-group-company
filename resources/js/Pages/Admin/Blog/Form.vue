<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    post: { type: Object, default: null },
});

const isEdit = !!defineProps.post;
</script>

<template>
    <Head :title="post ? 'แก้ไขบทความ' : 'เขียนบทความใหม่'" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">
                {{ post ? 'แก้ไขบทความ' : 'เขียนบทความใหม่' }}
            </h1>
            <Link href="/admin/blog" class="text-sm text-gray-600 hover:text-gray-900">&larr; กลับ</Link>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อบทความ</label>
                <input type="text" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="placeholder" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                <input type="text" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="placeholder" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ภาพปก</label>
                <div class="border-2 border-dashed rounded-md p-8 text-center text-gray-400 text-sm">
                    placeholder — image upload
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">เนื้อหา</label>
                <div class="border rounded-md h-64 flex items-center justify-center text-gray-400 text-sm">
                    placeholder — rich text editor
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">สถานะ</label>
                <select class="border rounded-md px-3 py-2 text-sm">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>
            <div class="flex gap-3 justify-end">
                <Link href="/admin/blog" class="px-4 py-2 text-sm border rounded-md text-gray-700 hover:bg-gray-50">
                    ยกเลิก
                </Link>
                <button class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    บันทึก
                </button>
            </div>
        </div>
    </AdminLayout>
</template>
