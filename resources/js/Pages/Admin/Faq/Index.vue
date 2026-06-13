<script setup>
import { ref, reactive } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    faqs: { type: Array, default: () => [] },
});

// ── Modal state ─────────────────────────────────────────────────────────────
const showModal = ref(false);
const editingFaq = ref(null);

const form = useForm({
    question: '',
    answer: '',
    status: 'active',
    sort_order: 0,
});

function openCreate() {
    editingFaq.value = null;
    form.reset();
    form.sort_order = props.faqs.length;
    showModal.value = true;
}

function openEdit(faq) {
    editingFaq.value = faq;
    form.question   = faq.question;
    form.answer     = faq.answer;
    form.status     = faq.status;
    form.sort_order = faq.sort_order;
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    editingFaq.value = null;
    form.reset();
}

function submit() {
    if (editingFaq.value) {
        form.put(route('admin.faq.update', editingFaq.value.id), {
            onSuccess: closeModal,
        });
    } else {
        form.post(route('admin.faq.store'), {
            onSuccess: closeModal,
        });
    }
}

// ── Toggle status ────────────────────────────────────────────────────────────
function toggleStatus(faq) {
    router.patch(route('admin.faq.toggle', faq.id));
}

// ── Delete ───────────────────────────────────────────────────────────────────
function deleteFaq(faq) {
    if (!confirm(`ลบคำถาม "${faq.question}" ?`)) return;
    router.delete(route('admin.faq.destroy', faq.id));
}

// ── Reorder ──────────────────────────────────────────────────────────────────
const draggingId = ref(null);

function onDragStart(faq) {
    draggingId.value = faq.id;
}

function onDrop(targetFaq) {
    if (draggingId.value === targetFaq.id) return;

    const list = [...props.faqs];
    const fromIdx = list.findIndex(f => f.id === draggingId.value);
    const toIdx   = list.findIndex(f => f.id === targetFaq.id);

    list.splice(toIdx, 0, list.splice(fromIdx, 1)[0]);

    const items = list.map((f, i) => ({ id: f.id, sort_order: i }));
    router.post(route('admin.faq.reorder'), { items });
    draggingId.value = null;
}
</script>

<template>
    <Head title="Admin — FAQ" />
    <AdminLayout>

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">จัดการ FAQ</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ faqs.length }} รายการ</p>
            </div>
            <button @click="openCreate"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                เพิ่มคำถาม
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">ลำดับ</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">คำถาม</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">สถานะ</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-36">สร้างโดย</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-36">แก้ไขล่าสุดโดย</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-if="faqs.length === 0">
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400">ยังไม่มีคำถาม — กดเพิ่มคำถามใหม่</td>
                    </tr>
                    <tr
                        v-for="faq in faqs"
                        :key="faq.id"
                        draggable="true"
                        @dragstart="onDragStart(faq)"
                        @dragover.prevent
                        @drop="onDrop(faq)"
                        class="hover:bg-gray-50 transition-colors cursor-grab"
                        :class="draggingId === faq.id ? 'opacity-40' : ''"
                    >
                        <!-- Sort order handle -->
                        <td class="px-4 py-3 text-gray-400">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M8 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM8 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM8 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM20 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM20 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM20 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/></svg>
                                <span class="text-xs font-mono">{{ faq.sort_order }}</span>
                            </div>
                        </td>

                        <!-- Question + Answer preview -->
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900 leading-snug">{{ faq.question }}</p>
                            <p class="text-gray-400 text-xs mt-0.5 line-clamp-1">{{ faq.answer }}</p>
                        </td>

                        <!-- Status toggle -->
                        <td class="px-4 py-3">
                            <button
                                @click="toggleStatus(faq)"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border transition"
                                :class="faq.status === 'active'
                                    ? 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100'
                                    : 'bg-gray-100 text-gray-500 border-gray-200 hover:bg-gray-200'"
                            >
                                <span class="w-1.5 h-1.5 rounded-full"
                                      :class="faq.status === 'active' ? 'bg-green-500' : 'bg-gray-400'"></span>
                                {{ faq.status === 'active' ? 'แสดง' : 'ซ่อน' }}
                            </button>
                        </td>

                        <!-- Created by -->
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ faq.created_by?.name ?? '—' }}
                        </td>

                        <!-- Updated by -->
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ faq.updated_by?.name ?? '—' }}
                        </td>

                        <!-- Actions -->
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="openEdit(faq)"
                                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931ZM16.862 4.487 19.5 7.125"/></svg>
                                </button>
                                <button @click="deleteFaq(faq)"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-md transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Drag hint -->
        <p v-if="faqs.length > 1" class="text-xs text-gray-400 mt-3 text-center">
            ลากแถวเพื่อจัดเรียงลำดับการแสดงผล
        </p>

        <!-- ── Modal ──────────────────────────────────────────────────────────── -->
        <Teleport to="body">
            <div v-if="showModal"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4"
                 @click.self="closeModal">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/50"></div>

                <!-- Panel -->
                <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-xl max-h-[90vh] overflow-y-auto">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900">
                            {{ editingFaq ? 'แก้ไขคำถาม' : 'เพิ่มคำถามใหม่' }}
                        </h2>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form @submit.prevent="submit" class="px-6 py-5 space-y-5">

                        <!-- Question -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                คำถาม <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.question"
                                type="text"
                                maxlength="500"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="พิมพ์คำถาม..."
                            />
                            <p v-if="form.errors.question" class="text-red-500 text-xs mt-1">{{ form.errors.question }}</p>
                        </div>

                        <!-- Answer -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                คำตอบ <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                v-model="form.answer"
                                rows="4"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                placeholder="พิมพ์คำตอบ..."
                            ></textarea>
                            <p v-if="form.errors.answer" class="text-red-500 text-xs mt-1">{{ form.errors.answer }}</p>
                        </div>

                        <!-- Sort order + Status -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">ลำดับ</label>
                                <input
                                    v-model.number="form.sort_order"
                                    type="number"
                                    min="0"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                />
                                <p v-if="form.errors.sort_order" class="text-red-500 text-xs mt-1">{{ form.errors.sort_order }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">สถานะ</label>
                                <select
                                    v-model="form.status"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white"
                                >
                                    <option value="active">แสดง (Active)</option>
                                    <option value="inactive">ซ่อน (Inactive)</option>
                                </select>
                                <p v-if="form.errors.status" class="text-red-500 text-xs mt-1">{{ form.errors.status }}</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                            <button type="button" @click="closeModal"
                                    class="px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                ยกเลิก
                            </button>
                            <button type="submit"
                                    :disabled="form.processing"
                                    class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-60 transition">
                                {{ form.processing ? 'กำลังบันทึก...' : 'บันทึก' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

    </AdminLayout>
</template>
