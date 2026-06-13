<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    portfolios: { type: Array, default: () => [] },
});

const draggingId = ref(null);

function onDragStart(p) { draggingId.value = p.id; }

function onDrop(target) {
    if (draggingId.value === target.id) return;
    const list = [...props.portfolios];
    const from = list.findIndex(p => p.id === draggingId.value);
    const to   = list.findIndex(p => p.id === target.id);
    list.splice(to, 0, list.splice(from, 1)[0]);
    router.post(route('admin.portfolio.reorder'), {
        items: list.map((p, i) => ({ id: p.id, sort_order: i })),
    });
    draggingId.value = null;
}

function toggleStatus(p) {
    router.patch(route('admin.portfolio.toggle', p.id));
}

function deletePortfolio(p) {
    if (!confirm(`ลบผลงาน "${p.title}" ?`)) return;
    router.delete(route('admin.portfolio.destroy', p.id));
}

function formatSize(bytes) {
    if (!bytes) return '—';
    return (bytes / 1024 / 1024).toFixed(1) + ' MB';
}
</script>

<template>
    <Head title="Admin — จัดการผลงาน" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">จัดการผลงาน</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ portfolios.length }} ผลงาน</p>
            </div>
            <Link :href="route('admin.portfolio.create')"
                  class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                เพิ่มผลงาน
            </Link>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="w-8 px-3 py-3"></th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-20">วิดีโอ</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ชื่อผลงาน</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">แพ็กเกจ</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">ขนาดไฟล์</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">สถานะ</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-if="portfolios.length === 0">
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                            ยังไม่มีผลงาน — กดเพิ่มผลงานใหม่
                        </td>
                    </tr>

                    <tr
                        v-for="p in portfolios"
                        :key="p.id"
                        draggable="true"
                        @dragstart="onDragStart(p)"
                        @dragover.prevent
                        @drop="onDrop(p)"
                        class="hover:bg-gray-50 transition-colors"
                        :class="draggingId === p.id ? 'opacity-40' : ''"
                    >
                        <!-- Drag handle -->
                        <td class="pl-3 pr-1 py-3 text-gray-300 cursor-grab">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM8 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM8 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM20 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM20 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM20 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/>
                            </svg>
                        </td>

                        <!-- Thumbnail / Video preview -->
                        <td class="px-4 py-3">
                            <div class="w-16 h-24 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center">
                                <video
                                    v-if="p.video_url"
                                    :src="p.video_url"
                                    :poster="p.thumbnail_url || undefined"
                                    class="w-full h-full object-cover"
                                    muted
                                    preload="none"
                                    @mouseenter="e => e.target.play()"
                                    @mouseleave="e => { e.target.pause(); e.target.currentTime = 0; }"
                                />
                                <svg v-else class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125h7.5c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125"/>
                                </svg>
                            </div>
                        </td>

                        <!-- Title + URL -->
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ p.title }}</p>
                            <a v-if="p.client_url" :href="p.client_url" target="_blank"
                               class="text-xs text-blue-500 hover:underline truncate block max-w-xs">
                                {{ p.client_url }}
                            </a>
                        </td>

                        <!-- Package -->
                        <td class="px-4 py-3">
                            <span v-if="p.package_name"
                                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                {{ p.package_name }}
                            </span>
                            <span v-else class="text-gray-400 text-xs">—</span>
                        </td>

                        <!-- File size -->
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ formatSize(p.file_size) }}</td>

                        <!-- Status -->
                        <td class="px-4 py-3">
                            <button @click="toggleStatus(p)"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border transition"
                                    :class="p.status === 'active'
                                        ? 'bg-green-50 text-green-700 border-green-200'
                                        : 'bg-gray-100 text-gray-500 border-gray-200'">
                                <span class="w-1.5 h-1.5 rounded-full"
                                      :class="p.status === 'active' ? 'bg-green-500' : 'bg-gray-400'"></span>
                                {{ p.status === 'active' ? 'แสดง' : 'ซ่อน' }}
                            </button>
                        </td>

                        <!-- Actions -->
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <Link :href="route('admin.portfolio.edit', p.id)"
                                      class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                                    </svg>
                                </Link>
                                <button @click="deletePortfolio(p)"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-md transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p v-if="portfolios.length > 1" class="text-xs text-gray-400 mt-3 text-center">
            ลากแถวเพื่อจัดเรียงลำดับการแสดงผล
        </p>
    </AdminLayout>
</template>
