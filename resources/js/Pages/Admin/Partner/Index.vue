<script setup>
import { ref, reactive, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    partners: { type: Array,  default: () => [] },
    settings: { type: Object, default: () => ({}) },
});

// ─── Tabs ─────────────────────────────────────────────────────────────────────
const activeTab = ref('list');

// ─── Slide-over panel ─────────────────────────────────────────────────────────
const panelOpen  = ref(false);
const editTarget = ref(null);
const processing = ref(false);
const errors     = ref({});

const form = reactive({
    name:        '',
    website_url: '',
    status:      'active',
    logo:        null,
});

const logoPreview = ref(null);
const logoInput   = ref(null);

function openCreate() {
    editTarget.value  = null;
    form.name         = '';
    form.website_url  = '';
    form.status       = 'active';
    form.logo         = null;
    logoPreview.value = null;
    errors.value      = {};
    panelOpen.value   = true;
}

function openEdit(item) {
    editTarget.value  = item;
    form.name         = item.name;
    form.website_url  = item.website_url ?? '';
    form.status       = item.status;
    form.logo         = null;
    logoPreview.value = item.logo_url ?? null;
    errors.value      = {};
    panelOpen.value   = true;
}

function closePanel() { panelOpen.value = false; }

function onLogoChange(e) {
    const f = e.target.files?.[0];
    if (!f) return;
    form.logo = f;
    logoPreview.value = URL.createObjectURL(f);
}

function buildFormData() {
    const fd = new FormData();
    fd.append('name',        form.name);
    fd.append('website_url', form.website_url);
    fd.append('status',      form.status);
    if (form.logo) fd.append('logo', form.logo);
    return fd;
}

function submitPanel() {
    errors.value = {};
    if (!form.name.trim()) { errors.value.name = 'กรุณาใส่ชื่อ'; return; }

    processing.value = true;
    const url = editTarget.value
        ? route('admin.partners.update', editTarget.value.id)
        : route('admin.partners.store');

    router.post(url, buildFormData(), {
        forceFormData:  true,
        onSuccess:      () => closePanel(),
        onError:        (e) => { errors.value = e; },
        onFinish:       () => { processing.value = false; },
        preserveScroll: true,
    });
}

function deleteItem(item) {
    if (!confirm(`ลบ "${item.name}" ?`)) return;
    router.delete(route('admin.partners.destroy', item.id), { preserveScroll: true });
}

function toggleStatus(item) {
    router.patch(route('admin.partners.toggle', item.id), {}, { preserveScroll: true });
}

// ─── Drag-to-reorder ─────────────────────────────────────────────────────────
const draggingId = ref(null);
const items      = ref([...props.partners]);

watch(() => props.partners, (v) => { items.value = [...v]; }, { deep: true });

function onDragStart(item) { draggingId.value = item.id; }
function onDragOver(e, item) {
    e.preventDefault();
    if (draggingId.value === item.id) return;
    const from = items.value.findIndex(i => i.id === draggingId.value);
    const to   = items.value.findIndex(i => i.id === item.id);
    if (from < 0 || to < 0) return;
    const copy = [...items.value];
    copy.splice(to, 0, copy.splice(from, 1)[0]);
    items.value = copy;
}
function onDrop() {
    draggingId.value = null;
    router.post(route('admin.partners.reorder'), {
        ids: items.value.map(i => i.id),
    }, { preserveScroll: true });
}

// ─── Settings ─────────────────────────────────────────────────────────────────
const settingsProcessing = ref(false);
const settingsErrors     = ref({});
const sf = reactive({
    heading:       props.settings.heading       ?? 'ลูกค้าที่ให้ความไว้วางใจกับเรา',
    bg_color:      props.settings.bg_color      ?? '#7c1d1d',
    logos_per_row: props.settings.logos_per_row ?? 4,
    show_name:     props.settings.show_name     ?? false,
    grayscale:     props.settings.grayscale     ?? true,
});

function submitSettings() {
    settingsErrors.value     = {};
    settingsProcessing.value = true;
    router.post(route('admin.partners.settings'), { ...sf }, {
        onError:  (e) => { settingsErrors.value = e; },
        onFinish: () => { settingsProcessing.value = false; },
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Admin — พาร์ทเนอร์ / ลูกค้า" />
    <AdminLayout>

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">พาร์ทเนอร์ / ลูกค้า</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ partners.length }} รายการ</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex gap-1 bg-gray-100 p-1 rounded-xl">
                    <button @click="activeTab = 'list'"
                            class="px-4 py-1.5 rounded-lg text-sm font-medium transition"
                            :class="activeTab === 'list' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'">
                        รายการ
                    </button>
                    <button @click="activeTab = 'settings'"
                            class="px-4 py-1.5 rounded-lg text-sm font-medium transition"
                            :class="activeTab === 'settings' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'">
                        ตั้งค่า Section
                    </button>
                </div>
                <button v-if="activeTab === 'list'" @click="openCreate"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    เพิ่ม Logo
                </button>
            </div>
        </div>

        <!-- ── LIST TAB ────────────────────────────────────────────────── -->
        <template v-if="activeTab === 'list'">

            <!-- Logo grid (drag-sort) -->
            <div v-if="items.length"
                 class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                <div v-for="item in items" :key="item.id"
                     draggable="true"
                     @dragstart="onDragStart(item)"
                     @dragover="onDragOver($event, item)"
                     @drop="onDrop"
                     @dragend="onDrop"
                     class="group relative bg-white rounded-xl shadow-sm p-4 flex flex-col items-center gap-2 cursor-grab active:cursor-grabbing transition"
                     :class="[
                         draggingId === item.id ? 'opacity-40 scale-95' : 'hover:shadow-md',
                         item.status === 'inactive' ? 'opacity-50' : '',
                     ]">

                    <!-- Logo -->
                    <div class="w-full h-16 flex items-center justify-center overflow-hidden">
                        <img v-if="item.logo_url" :src="item.logo_url" :alt="item.name"
                             class="max-h-full max-w-full object-contain"/>
                        <div v-else class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z"/></svg>
                        </div>
                    </div>

                    <p class="text-xs font-medium text-gray-700 text-center truncate w-full">{{ item.name }}</p>

                    <!-- Hover actions overlay -->
                    <div class="absolute inset-0 bg-white/95 rounded-xl opacity-0 group-hover:opacity-100 transition flex flex-col items-center justify-center gap-2 p-2">
                        <div class="flex gap-1.5">
                            <button @click.stop="toggleStatus(item)"
                                    class="px-2 py-1 rounded-lg text-xs font-semibold transition"
                                    :class="item.status === 'active'
                                        ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                        : 'bg-gray-100 text-gray-500 hover:bg-gray-200'">
                                {{ item.status === 'active' ? 'แสดง' : 'ซ่อน' }}
                            </button>
                        </div>
                        <div class="flex gap-1.5">
                            <button @click.stop="openEdit(item)"
                                    class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                            </button>
                            <button @click.stop="deleteItem(item)"
                                    class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Drag handle hint -->
                    <div class="absolute top-1.5 left-1.5 text-gray-200 group-hover:opacity-0 transition">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm6 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm6 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm6 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg>
                    </div>
                </div>
            </div>

            <div v-else class="bg-white rounded-xl shadow-sm p-12 text-center text-gray-400">
                ยังไม่มี logo — กด "เพิ่ม Logo" เพื่อเริ่มต้น
            </div>
        </template>

        <!-- ── SETTINGS TAB ─────────────────────────────────────────────── -->
        <template v-else>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <div class="bg-white rounded-xl shadow-sm p-6 space-y-5">
                    <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">ข้อความ &amp; สี</h2>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Heading</label>
                        <input v-model="sf.heading" type="text" maxlength="200"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        <p v-if="settingsErrors.heading" class="text-red-500 text-xs mt-1">{{ settingsErrors.heading }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">สีพื้นหลัง Section</label>
                        <div class="flex items-center gap-3">
                            <input type="color" v-model="sf.bg_color"
                                   class="w-10 h-10 rounded-lg border border-gray-300 cursor-pointer p-0.5"/>
                            <input v-model="sf.bg_color" type="text" maxlength="7"
                                   class="w-28 border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            <div class="flex-1 h-10 rounded-xl border border-gray-200" :style="`background: ${sf.bg_color}`"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 space-y-5">
                    <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">การแสดงผล</h2>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-2">Logo ต่อแถว (Grid)</label>
                        <div class="flex items-center gap-3">
                            <input type="range" v-model.number="sf.logos_per_row" min="2" max="6" step="1"
                                   class="flex-1 accent-blue-600"/>
                            <span class="w-8 text-center text-sm font-bold text-gray-700">{{ sf.logos_per_row }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-400 mt-1">
                            <span>2</span><span>3</span><span>4</span><span>5</span><span>6</span>
                        </div>
                    </div>

                    <label class="flex items-center justify-between cursor-pointer select-none py-2 border-t border-gray-100">
                        <div>
                            <p class="text-sm font-medium text-gray-700">แสดงชื่อแบรนด์ใต้ Logo</p>
                            <p class="text-xs text-gray-400">แสดงข้อความชื่อใต้รูป logo</p>
                        </div>
                        <div class="relative flex-shrink-0">
                            <input type="checkbox" v-model="sf.show_name" class="sr-only peer"/>
                            <div class="w-10 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full transition"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-4 shadow"></div>
                        </div>
                    </label>

                    <label class="flex items-center justify-between cursor-pointer select-none py-2 border-t border-gray-100">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Grayscale → Color on hover</p>
                            <p class="text-xs text-gray-400">Logo เป็น grayscale แล้วเป็นสีเมื่อ hover</p>
                        </div>
                        <div class="relative flex-shrink-0">
                            <input type="checkbox" v-model="sf.grayscale" class="sr-only peer"/>
                            <div class="w-10 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full transition"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-4 shadow"></div>
                        </div>
                    </label>
                </div>

                <!-- Preview -->
                <div class="lg:col-span-2 rounded-2xl overflow-hidden p-8" :style="`background: ${sf.bg_color}`">
                    <h2 class="text-2xl font-bold text-white font-kanit mb-2">{{ sf.heading }}</h2>
                    <p class="text-white/40 text-sm">— preview สี Section —</p>
                </div>

                <div class="lg:col-span-2 flex justify-end">
                    <button @click="submitSettings" :disabled="settingsProcessing"
                            class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white font-semibold px-8 py-2.5 rounded-xl transition text-sm">
                        {{ settingsProcessing ? 'กำลังบันทึก...' : 'บันทึกการตั้งค่า' }}
                    </button>
                </div>
            </div>
        </template>

        <!-- ── SLIDE-OVER PANEL ─────────────────────────────────────────── -->
        <Teleport to="body">
            <Transition enter-from-class="translate-x-full" leave-to-class="translate-x-full"
                        enter-active-class="transition duration-300" leave-active-class="transition duration-300">
                <div v-if="panelOpen" class="fixed inset-0 z-50 flex justify-end">
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closePanel"></div>
                    <div class="relative w-full max-w-sm bg-white h-full overflow-y-auto shadow-2xl flex flex-col">

                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                            <h2 class="font-bold text-gray-900">{{ editTarget ? 'แก้ไข Partner' : 'เพิ่ม Partner ใหม่' }}</h2>
                            <button @click="closePanel" class="text-gray-400 hover:text-gray-700 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="flex-1 px-6 py-5 space-y-5">

                            <!-- Logo upload -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-2">Logo *</label>

                                <!-- Preview -->
                                <div v-if="logoPreview"
                                     class="relative flex items-center justify-center h-32 bg-gray-50 rounded-xl border border-gray-200 mb-2 p-4">
                                    <img :src="logoPreview" class="max-h-full max-w-full object-contain"/>
                                    <button type="button"
                                            @click="logoPreview = null; form.logo = null"
                                            class="absolute top-2 right-2 bg-red-50 hover:bg-red-100 text-red-500 rounded-full p-1 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>

                                <div @click="logoInput.click()"
                                     class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-xl py-8 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                                    <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                                    <p class="text-sm text-gray-500">{{ logoPreview ? 'เปลี่ยน Logo' : 'คลิกเพื่อเลือก Logo' }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">PNG / SVG / WebP / JPG ไม่เกิน 2 MB</p>
                                </div>
                                <input ref="logoInput" type="file"
                                       accept="image/png,image/jpeg,image/webp,image/gif,image/svg+xml"
                                       class="hidden" @change="onLogoChange"/>
                                <p v-if="errors.logo" class="text-red-500 text-xs mt-1">{{ errors.logo }}</p>
                            </div>

                            <!-- Name -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">ชื่อแบรนด์ *</label>
                                <input v-model="form.name" type="text" maxlength="100"
                                       placeholder="เช่น PASAYA, Music Collection"
                                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       :class="errors.name ? 'border-red-400' : 'border-gray-300'"/>
                                <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</p>
                            </div>

                            <!-- Website URL -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">URL เว็บไซต์ (ไม่บังคับ)</label>
                                <input v-model="form.website_url" type="text"
                                       placeholder="https://example.com"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                                <p v-if="errors.website_url" class="text-red-500 text-xs mt-1">{{ errors.website_url }}</p>
                            </div>

                            <!-- Status -->
                            <label class="flex items-center gap-2 cursor-pointer select-none pt-1">
                                <div class="relative">
                                    <input type="checkbox"
                                           :checked="form.status === 'active'"
                                           @change="form.status = $event.target.checked ? 'active' : 'inactive'"
                                           class="sr-only peer"/>
                                    <div class="w-10 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full transition"></div>
                                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-4 shadow"></div>
                                </div>
                                <span class="text-sm text-gray-700">แสดงบนหน้าเว็บ</span>
                            </label>
                        </div>

                        <div class="px-6 py-4 border-t border-gray-200 flex gap-3">
                            <button type="button" @click="closePanel"
                                    class="flex-1 py-2.5 rounded-xl border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                ยกเลิก
                            </button>
                            <button type="button" @click="submitPanel" :disabled="processing"
                                    class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-sm font-semibold text-white transition">
                                {{ processing ? 'กำลังบันทึก...' : (editTarget ? 'บันทึก' : 'เพิ่ม') }}
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

    </AdminLayout>
</template>
