<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    packages: { type: Array, default: () => [] },
});

const draggingId = ref(null);

import { ref } from 'vue';

function onDragStart(pkg) { draggingId.value = pkg.id; }

function onDrop(target) {
    if (draggingId.value === target.id) return;
    const list = [...props.packages];
    const from = list.findIndex(p => p.id === draggingId.value);
    const to   = list.findIndex(p => p.id === target.id);
    list.splice(to, 0, list.splice(from, 1)[0]);
    router.post(route('admin.packages.reorder'), {
        items: list.map((p, i) => ({ id: p.id, sort_order: i })),
    });
    draggingId.value = null;
}

function toggleStatus(pkg) {
    router.patch(route('admin.packages.toggle', pkg.id));
}

function deletePkg(pkg) {
    if (!confirm(`ลบแพ็กเกจ "${pkg.name}" ?`)) return;
    router.delete(route('admin.packages.destroy', pkg.id));
}

const statusStyle = { active: 'bg-green-50 text-green-700 border-green-200', inactive: 'bg-gray-100 text-gray-500 border-gray-200' };
</script>

<template>
    <Head title="Admin — แพ็กเกจ" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">จัดการแพ็กเกจ</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ packages.length }} แพ็กเกจ</p>
            </div>
            <div class="flex gap-2">
                <Link :href="route('admin.packages.addons')"
                      class="px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    บริการเสริม (Add-ons)
                </Link>
                <Link :href="route('admin.packages.create')"
                      class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    เพิ่มแพ็กเกจ
                </Link>
            </div>
        </div>

        <!-- Package Cards Grid -->
        <div class="grid gap-4">
            <div v-if="packages.length === 0" class="bg-white rounded-xl p-12 text-center text-gray-400">
                ยังไม่มีแพ็กเกจ — กดเพิ่มแพ็กเกจใหม่
            </div>

            <div
                v-for="pkg in packages"
                :key="pkg.id"
                draggable="true"
                @dragstart="onDragStart(pkg)"
                @dragover.prevent
                @drop="onDrop(pkg)"
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-start gap-5 transition hover:shadow-md cursor-grab"
                :class="draggingId === pkg.id ? 'opacity-40' : ''"
            >
                <!-- Drag Handle + Order -->
                <div class="flex-shrink-0 flex flex-col items-center gap-1 pt-1">
                    <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M8 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM8 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM8 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM20 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM20 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM20 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/></svg>
                    <span class="text-xs text-gray-300 font-mono">{{ pkg.sort_order }}</span>
                </div>

                <!-- Color Swatch -->
                <div class="flex-shrink-0 w-12 h-12 rounded-xl"
                     :style="`background: linear-gradient(135deg, ${pkg.color_from || '#94a3b8'}, ${pkg.color_to || '#64748b'})`">
                </div>

                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-bold text-gray-900">{{ pkg.name }}</h3>
                        <span v-if="pkg.badge_text"
                              class="text-xs font-bold px-2 py-0.5 rounded-full text-white"
                              :style="`background: ${pkg.badge_color || '#ef4444'}`">
                            {{ pkg.badge_text }}
                        </span>
                        <span class="text-xs text-gray-400 font-mono">/{{ pkg.slug }}</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-0.5">{{ pkg.page_structure }}</p>
                    <div class="flex items-center gap-4 mt-2 text-sm">
                        <span class="font-bold text-gray-900 text-base">
                            <span v-if="pkg.original_price" class="line-through text-gray-400 text-sm font-normal mr-1">
                                {{ pkg.original_price.toLocaleString() }}
                            </span>
                            {{ pkg.price.toLocaleString() }} ฿
                        </span>
                        <span class="text-gray-400">{{ pkg.features?.length ?? 0 }} features</span>
                    </div>
                </div>

                <!-- Status + Actions -->
                <div class="flex-shrink-0 flex items-center gap-2">
                    <button @click="toggleStatus(pkg)"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border transition"
                            :class="statusStyle[pkg.status]">
                        <span class="w-1.5 h-1.5 rounded-full" :class="pkg.status === 'active' ? 'bg-green-500' : 'bg-gray-400'"></span>
                        {{ pkg.status === 'active' ? 'แสดง' : 'ซ่อน' }}
                    </button>
                    <Link :href="route('admin.packages.edit', pkg.id)"
                          class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                    </Link>
                    <button @click="deletePkg(pkg)"
                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-md transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <p v-if="packages.length > 1" class="text-xs text-gray-400 mt-3 text-center">ลากแถวเพื่อจัดเรียงลำดับการแสดงผล</p>
    </AdminLayout>
</template>
