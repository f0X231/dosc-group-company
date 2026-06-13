<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    addons: { type: Array, default: () => [] },
});

const showModal = ref(false);
const editingAddon = ref(null);

const form = useForm({
    name: '', description: '', price: '', status: 'active', sort_order: 0,
});

function openCreate() {
    editingAddon.value = null;
    form.reset();
    form.sort_order = props.addons.length;
    showModal.value = true;
}

function openEdit(addon) {
    editingAddon.value = addon;
    form.name        = addon.name;
    form.description = addon.description ?? '';
    form.price       = addon.price;
    form.status      = addon.status;
    form.sort_order  = addon.sort_order;
    showModal.value  = true;
}

function closeModal() { showModal.value = false; editingAddon.value = null; form.reset(); }

function submit() {
    if (editingAddon.value) {
        form.put(route('admin.packages.addons.update', editingAddon.value.id), { onSuccess: closeModal });
    } else {
        form.post(route('admin.packages.addons.store'), { onSuccess: closeModal });
    }
}

function toggleStatus(addon) { router.patch(route('admin.packages.addons.toggle', addon.id)); }
function deleteAddon(addon) {
    if (!confirm(`ลบ "${addon.name}" ?`)) return;
    router.delete(route('admin.packages.addons.destroy', addon.id));
}
</script>

<template>
    <Head title="Admin — บริการเสริม" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <Link :href="route('admin.packages')" class="text-gray-400 hover:text-gray-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">บริการเสริม (Add-ons)</h1>
                    <p class="text-sm text-gray-500 mt-0.5">{{ addons.length }} รายการ</p>
                </div>
            </div>
            <button @click="openCreate"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                เพิ่มบริการเสริม
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ชื่อ</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">รายละเอียด</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">ราคา (฿)</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">สถานะ</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-if="addons.length === 0">
                        <td colspan="5" class="px-4 py-12 text-center text-gray-400">ยังไม่มีบริการเสริม</td>
                    </tr>
                    <tr v-for="addon in addons" :key="addon.id" class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ addon.name }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs max-w-xs">
                            <p class="line-clamp-2">{{ addon.description || '—' }}</p>
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-900">{{ addon.price.toLocaleString() }}</td>
                        <td class="px-4 py-3">
                            <button @click="toggleStatus(addon)"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border transition"
                                    :class="addon.status === 'active' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-100 text-gray-500 border-gray-200'">
                                <span class="w-1.5 h-1.5 rounded-full" :class="addon.status === 'active' ? 'bg-green-500' : 'bg-gray-400'"></span>
                                {{ addon.status === 'active' ? 'แสดง' : 'ซ่อน' }}
                            </button>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button @click="openEdit(addon)" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                </button>
                                <button @click="deleteAddon(addon)" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-md transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <Teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="closeModal">
                <div class="absolute inset-0 bg-black/50"></div>
                <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md">
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <h2 class="text-lg font-bold text-gray-900">{{ editingAddon ? 'แก้ไขบริการเสริม' : 'เพิ่มบริการเสริม' }}</h2>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form @submit.prevent="submit" class="px-6 py-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">ชื่อ *</label>
                            <input v-model="form.name" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">รายละเอียด</label>
                            <textarea v-model="form.description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">ราคา (฿) *</label>
                                <input v-model.number="form.price" type="number" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">สถานะ</label>
                                <select v-model="form.status" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="active">แสดง</option>
                                    <option value="inactive">ซ่อน</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex gap-3 pt-2 border-t border-gray-100">
                            <button type="button" @click="closeModal" class="flex-1 px-4 py-2.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition">ยกเลิก</button>
                            <button type="submit" :disabled="form.processing" class="flex-1 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white text-sm font-semibold py-2.5 rounded-lg transition">
                                {{ form.processing ? 'กำลังบันทึก...' : 'บันทึก' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>
