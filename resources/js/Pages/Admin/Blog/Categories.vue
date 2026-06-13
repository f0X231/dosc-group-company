<script setup>
import { ref, reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    categories: { type: Array, default: () => [] },
});

// ─── Modal state ──────────────────────────────────────────────────────────────
const showModal  = ref(false);
const editTarget = ref(null);
const processing = ref(false);
const errors     = ref({});

const COLORS = [
    '#3b82f6','#ef4444','#10b981','#f59e0b','#8b5cf6',
    '#ec4899','#06b6d4','#84cc16','#f97316','#6366f1',
];

const form = reactive({ name: '', color: '#3b82f6', description: '' });

function openCreate() {
    editTarget.value = null;
    form.name        = '';
    form.color       = '#3b82f6';
    form.description = '';
    errors.value     = {};
    showModal.value  = true;
}

function openEdit(cat) {
    editTarget.value = cat;
    form.name        = cat.name;
    form.color       = cat.color;
    form.description = cat.description ?? '';
    errors.value     = {};
    showModal.value  = true;
}

function closeModal() { showModal.value = false; }

function submit() {
    errors.value = {};
    if (!form.name.trim()) { errors.value.name = 'กรุณาใส่ชื่อหมวดหมู่'; return; }

    processing.value = true;

    const payload = { name: form.name.trim(), color: form.color, description: form.description };

    if (editTarget.value) {
        router.put(route('admin.blog.categories.update', editTarget.value.id), payload, {
            onSuccess: () => { closeModal(); },
            onError: (e) => { errors.value = e; },
            onFinish: () => { processing.value = false; },
            preserveScroll: true,
        });
    } else {
        router.post(route('admin.blog.categories.store'), payload, {
            onSuccess: () => { closeModal(); },
            onError: (e) => { errors.value = e; },
            onFinish: () => { processing.value = false; },
            preserveScroll: true,
        });
    }
}

function deleteCategory(cat) {
    if (!confirm(`ลบหมวดหมู่ "${cat.name}" ?\nบทความที่อยู่ในหมวดนี้จะไม่มีหมวดหมู่`)) return;
    router.delete(route('admin.blog.categories.destroy', cat.id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Admin — หมวดหมู่บทความ" />
    <AdminLayout>

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">หมวดหมู่บทความ</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ categories.length }} หมวดหมู่</p>
            </div>
            <button @click="openCreate"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                เพิ่มหมวดหมู่
            </button>
        </div>

        <!-- Category grid -->
        <div v-if="categories.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="cat in categories" :key="cat.id"
                 class="bg-white rounded-xl shadow-sm p-5 flex items-start gap-4">
                <!-- Color dot -->
                <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-base"
                     :style="`background: ${cat.color}`">
                    {{ cat.name.charAt(0) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 truncate">{{ cat.name }}</p>
                    <p class="text-xs text-gray-400 font-mono mt-0.5">{{ cat.slug }}</p>
                    <p v-if="cat.description" class="text-xs text-gray-500 mt-1 line-clamp-2">{{ cat.description }}</p>
                </div>
                <div class="flex-shrink-0 flex gap-1">
                    <button @click="openEdit(cat)"
                            class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                        </svg>
                    </button>
                    <button @click="deleteCategory(cat)"
                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center text-gray-400">
            ยังไม่มีหมวดหมู่ — กด "เพิ่มหมวดหมู่" เพื่อเริ่มต้น
        </div>

        <!-- Modal -->
        <Teleport to="body">
            <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" enter-active-class="transition duration-150" leave-active-class="transition duration-150">
                <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center px-4">
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeModal"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-5">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-900">
                                {{ editTarget ? 'แก้ไขหมวดหมู่' : 'เพิ่มหมวดหมู่ใหม่' }}
                            </h2>
                            <button @click="closeModal" class="text-gray-400 hover:text-gray-700 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">ชื่อหมวดหมู่ *</label>
                            <input v-model="form.name" type="text" placeholder="เช่น เว็บไซต์, การตลาด, SEO"
                                   class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   :class="errors.name ? 'border-red-400' : 'border-gray-300'"
                                   @keydown.enter="submit"/>
                            <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</p>
                        </div>

                        <!-- Color -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">สีหมวดหมู่</label>
                            <div class="flex flex-wrap gap-2 mb-2">
                                <button v-for="c in COLORS" :key="c" type="button"
                                        @click="form.color = c"
                                        class="w-8 h-8 rounded-full transition ring-offset-2"
                                        :class="form.color === c ? 'ring-2 ring-gray-400 scale-110' : 'hover:scale-110'"
                                        :style="`background: ${c}`"/>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex-shrink-0 border border-gray-200" :style="`background: ${form.color}`"></div>
                                <input v-model="form.color" type="text" placeholder="#3b82f6"
                                       class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">คำอธิบาย (ไม่บังคับ)</label>
                            <textarea v-model="form.description" rows="2" placeholder="อธิบายหมวดหมู่นี้..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"/>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="closeModal"
                                    class="flex-1 py-2.5 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                ยกเลิก
                            </button>
                            <button type="button" @click="submit" :disabled="processing"
                                    class="flex-1 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-sm font-semibold text-white transition">
                                {{ processing ? 'กำลังบันทึก...' : (editTarget ? 'บันทึกการแก้ไข' : 'เพิ่มหมวดหมู่') }}
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

    </AdminLayout>
</template>
