<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    banners: { type: Array, default: () => [] },
});

const draggingId = ref(null);

function onDragStart(b) { draggingId.value = b.id; }
function onDrop(target) {
    if (draggingId.value === target.id) return;
    const list = [...props.banners];
    const from = list.findIndex(b => b.id === draggingId.value);
    const to   = list.findIndex(b => b.id === target.id);
    list.splice(to, 0, list.splice(from, 1)[0]);
    router.post(route('admin.hero-banner.reorder'), { items: list.map((b, i) => ({ id: b.id, sort_order: i })) });
    draggingId.value = null;
}

function toggleStatus(b) { router.patch(route('admin.hero-banner.toggle', b.id)); }
function deleteBanner(b) {
    if (!confirm(`ลบ Banner "${b.headline}" ?`)) return;
    router.delete(route('admin.hero-banner.destroy', b.id));
}

const mediaLabel = { none: 'ไม่มี', video: 'วิดีโอ', image_full: 'รูปเต็ม', image_side: 'รูป Illustration' };
const animLabel  = { slide_up: 'Slide Up', typewriter: 'Typewriter', fade: 'Fade' };
</script>

<template>
    <Head title="Admin — Hero Banner" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Hero Banner</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ banners.length }} banner · แสดงผล banner แรกที่ status = active</p>
            </div>
            <Link :href="route('admin.hero-banner.create')"
                  class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                เพิ่ม Banner
            </Link>
        </div>

        <div class="space-y-3">
            <div v-if="banners.length === 0"
                 class="bg-white rounded-xl p-12 text-center text-gray-400">
                ยังไม่มี Banner — กดเพิ่มใหม่
            </div>

            <div
                v-for="b in banners"
                :key="b.id"
                draggable="true"
                @dragstart="onDragStart(b)"
                @dragover.prevent
                @drop="onDrop(b)"
                class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-4 transition hover:shadow-md"
                :class="draggingId === b.id ? 'opacity-40 cursor-grabbing' : 'cursor-grab'"
            >
                <!-- Drag handle -->
                <svg class="w-5 h-5 text-gray-300 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM8 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM8 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM20 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM20 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM20 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/>
                </svg>

                <!-- Color swatch preview -->
                <div class="flex-shrink-0 w-14 h-10 rounded-lg overflow-hidden"
                     :style="`background: linear-gradient(135deg, ${b.bg_color_from}, ${b.bg_color_to})`">
                    <div v-if="b.media_url && (b.media_type === 'image_full' || b.media_type === 'image_side')"
                         class="w-full h-full bg-cover bg-center"
                         :style="`background-image: url(${b.media_url})`"></div>
                    <div v-else-if="b.media_type === 'video'"
                         class="w-full h-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white/70" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                </div>

                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 truncate">{{ b.headline }}
                        <span v-if="b.headline_highlight" class="text-blue-600"> {{ b.headline_highlight }}</span>
                    </p>
                    <div class="flex items-center gap-3 mt-1 text-xs text-gray-400">
                        <span>{{ mediaLabel[b.media_type] }}</span>
                        <span>·</span>
                        <span>{{ animLabel[b.animation_style] }}</span>
                        <span v-if="b.stats?.length">· {{ b.stats.length }} stats</span>
                    </div>
                </div>

                <!-- Status + actions -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button @click="toggleStatus(b)"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border transition"
                            :class="b.status === 'active'
                                ? 'bg-green-50 text-green-700 border-green-200'
                                : 'bg-gray-100 text-gray-500 border-gray-200'">
                        <span class="w-1.5 h-1.5 rounded-full"
                              :class="b.status === 'active' ? 'bg-green-500' : 'bg-gray-400'"></span>
                        {{ b.status === 'active' ? 'Active' : 'Draft' }}
                    </button>
                    <Link :href="route('admin.hero-banner.edit', b.id)"
                          class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                        </svg>
                    </Link>
                    <button @click="deleteBanner(b)"
                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-md transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <p v-if="banners.length > 1" class="text-xs text-gray-400 mt-3 text-center">
            ลากเพื่อจัดลำดับ — banner แรกที่ Active จะแสดงบนหน้าแรก
        </p>
    </AdminLayout>
</template>
