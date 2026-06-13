<script setup>
import { ref, reactive, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    testimonials: { type: Array,  default: () => [] },
    settings:     { type: Object, default: () => ({}) },
});

// ─── Tabs ─────────────────────────────────────────────────────────────────────
const activeTab = ref('list'); // 'list' | 'settings'

// ─── Slide-over panel ─────────────────────────────────────────────────────────
const panelOpen  = ref(false);
const editTarget = ref(null);
const processing = ref(false);
const errors     = ref({});

const AVATAR_COLORS = [
    '#1a7a6e','#3b82f6','#ef4444','#8b5cf6',
    '#f59e0b','#10b981','#ec4899','#6366f1',
];

const SOURCE_ICONS = {
    direct:   null,
    google:   'Google',
    facebook: 'Facebook',
};

const form = reactive({
    customer_name:  '',
    customer_title: '',
    avatar_color:   '#1a7a6e',
    review_text:    '',
    rating:         5,
    source:         'direct',
    source_url:     '',
    status:         'active',
    avatar:         null,
});

const avatarPreview = ref(null);
const avatarInput   = ref(null);

function openCreate() {
    editTarget.value      = null;
    form.customer_name    = '';
    form.customer_title   = '';
    form.avatar_color     = '#1a7a6e';
    form.review_text      = '';
    form.rating           = 5;
    form.source           = 'direct';
    form.source_url       = '';
    form.status           = 'active';
    form.avatar           = null;
    avatarPreview.value   = null;
    errors.value          = {};
    panelOpen.value       = true;
}

function openEdit(item) {
    editTarget.value      = item;
    form.customer_name    = item.customer_name;
    form.customer_title   = item.customer_title ?? '';
    form.avatar_color     = item.avatar_color   ?? '#1a7a6e';
    form.review_text      = item.review_text;
    form.rating           = item.rating;
    form.source           = item.source;
    form.source_url       = item.source_url ?? '';
    form.status           = item.status;
    form.avatar           = null;
    avatarPreview.value   = item.avatar_url ?? null;
    errors.value          = {};
    panelOpen.value       = true;
}

function closePanel() { panelOpen.value = false; }

function onAvatarChange(e) {
    const f = e.target.files?.[0];
    if (!f) return;
    form.avatar = f;
    avatarPreview.value = URL.createObjectURL(f);
}

function buildFormData() {
    const fd = new FormData();
    fd.append('customer_name',  form.customer_name);
    fd.append('customer_title', form.customer_title);
    fd.append('avatar_color',   form.avatar_color);
    fd.append('review_text',    form.review_text);
    fd.append('rating',         form.rating);
    fd.append('source',         form.source);
    fd.append('source_url',     form.source_url);
    fd.append('status',         form.status);
    if (form.avatar) fd.append('avatar', form.avatar);
    return fd;
}

function submitPanel() {
    errors.value = {};
    if (!form.customer_name.trim()) { errors.value.customer_name = 'กรุณาใส่ชื่อ'; return; }
    if (!form.review_text.trim())   { errors.value.review_text   = 'กรุณาใส่ข้อความรีวิว'; return; }

    processing.value = true;
    const routeName = editTarget.value
        ? route('admin.testimonials.update', editTarget.value.id)
        : route('admin.testimonials.store');

    router.post(routeName, buildFormData(), {
        forceFormData: true,
        onSuccess:     () => { closePanel(); },
        onError:       (e) => { errors.value = e; },
        onFinish:      () => { processing.value = false; },
        preserveScroll: true,
    });
}

function deleteItem(item) {
    if (!confirm(`ลบรีวิวของ "${item.customer_name}" ?`)) return;
    router.delete(route('admin.testimonials.destroy', item.id), { preserveScroll: true });
}

function toggleStatus(item) {
    router.patch(route('admin.testimonials.toggle', item.id), {}, { preserveScroll: true });
}

// ─── Drag to reorder ──────────────────────────────────────────────────────────
const draggingId = ref(null);
const items      = ref([...props.testimonials]);

// Keep in sync with server updates
import { watch } from 'vue';
watch(() => props.testimonials, (v) => { items.value = [...v]; }, { deep: true });

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
    router.post(route('admin.testimonials.reorder'), {
        ids: items.value.map(i => i.id),
    }, { preserveScroll: true });
}

// ─── Settings form ────────────────────────────────────────────────────────────
const settingsProcessing = ref(false);
const settingsErrors     = ref({});
const settingsForm       = reactive({
    label:        props.settings.label        ?? 'TESTIMONIALS',
    heading:      props.settings.heading      ?? 'กำลังใจสำคัญของเรา',
    bg_color:     props.settings.bg_color     ?? '#7c1d1d',
    overlay_text: props.settings.overlay_text ?? '',
    cta_text:     props.settings.cta_text     ?? 'ดูเพิ่มเติม',
    cta_url:      props.settings.cta_url      ?? '',
    featured_image: null,
});
const featuredPreview   = ref(props.settings.featured_image_url ?? null);
const featuredInput     = ref(null);

function onFeaturedChange(e) {
    const f = e.target.files?.[0];
    if (!f) return;
    settingsForm.featured_image = f;
    featuredPreview.value = URL.createObjectURL(f);
}

function submitSettings() {
    settingsErrors.value   = {};
    settingsProcessing.value = true;
    const fd = new FormData();
    fd.append('label',        settingsForm.label);
    fd.append('heading',      settingsForm.heading);
    fd.append('bg_color',     settingsForm.bg_color);
    fd.append('overlay_text', settingsForm.overlay_text);
    fd.append('cta_text',     settingsForm.cta_text);
    fd.append('cta_url',      settingsForm.cta_url);
    if (settingsForm.featured_image) fd.append('featured_image', settingsForm.featured_image);

    router.post(route('admin.testimonials.settings'), fd, {
        forceFormData: true,
        onError:  (e) => { settingsErrors.value = e; },
        onFinish: () => { settingsProcessing.value = false; },
        preserveScroll: true,
    });
}

// ─── Stars helper ─────────────────────────────────────────────────────────────
function initial(name) { return name?.charAt(0)?.toUpperCase() ?? '?'; }
</script>

<template>
    <Head title="Admin — รีวิวจากลูกค้า" />
    <AdminLayout>

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">รีวิวจากลูกค้า</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ testimonials.length }} รีวิว</p>
            </div>
            <div class="flex items-center gap-2">
                <!-- Tabs -->
                <div class="flex gap-1 bg-gray-100 p-1 rounded-xl">
                    <button @click="activeTab = 'list'"
                            class="px-4 py-1.5 rounded-lg text-sm font-medium transition"
                            :class="activeTab === 'list' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'">
                        รีวิวทั้งหมด
                    </button>
                    <button @click="activeTab = 'settings'"
                            class="px-4 py-1.5 rounded-lg text-sm font-medium transition"
                            :class="activeTab === 'settings' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'">
                        ตั้งค่า Section
                    </button>
                </div>
                <button v-if="activeTab === 'list'"
                        @click="openCreate"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    เพิ่มรีวิว
                </button>
            </div>
        </div>

        <!-- ── LIST TAB ──────────────────────────────────────────────── -->
        <template v-if="activeTab === 'list'">
            <p v-if="!items.length" class="bg-white rounded-xl shadow-sm p-12 text-center text-gray-400">
                ยังไม่มีรีวิว — กด "เพิ่มรีวิว" เพื่อเริ่มต้น
            </p>

            <div v-else class="space-y-3">
                <div v-for="item in items" :key="item.id"
                     draggable="true"
                     @dragstart="onDragStart(item)"
                     @dragover="onDragOver($event, item)"
                     @drop="onDrop"
                     @dragend="onDrop"
                     class="bg-white rounded-xl shadow-sm p-4 flex items-start gap-4 cursor-grab active:cursor-grabbing transition"
                     :class="draggingId === item.id ? 'opacity-50 scale-98' : ''">

                    <!-- Drag handle -->
                    <div class="flex-shrink-0 text-gray-300 hover:text-gray-500 pt-1">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm6 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm6 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm6 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg>
                    </div>

                    <!-- Avatar -->
                    <div class="flex-shrink-0 w-11 h-11 rounded-full overflow-hidden flex items-center justify-center text-white font-bold text-lg"
                         :style="item.avatar_url ? '' : `background: ${item.avatar_color}`">
                        <img v-if="item.avatar_url" :src="item.avatar_url" class="w-full h-full object-cover"/>
                        <span v-else>{{ initial(item.customer_name) }}</span>
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-semibold text-gray-900">{{ item.customer_name }}</p>
                            <p v-if="item.customer_title" class="text-xs text-gray-400">{{ item.customer_title }}</p>
                            <span v-if="item.source !== 'direct'"
                                  class="text-xs px-2 py-0.5 rounded-full font-medium"
                                  :class="item.source === 'google' ? 'bg-blue-50 text-blue-600' : 'bg-indigo-50 text-indigo-600'">
                                {{ item.source === 'google' ? 'Google' : 'Facebook' }}
                            </span>
                        </div>
                        <!-- Stars -->
                        <div class="flex gap-0.5 mt-1">
                            <span v-for="s in 5" :key="s"
                                  class="text-sm"
                                  :class="s <= item.rating ? 'text-amber-400' : 'text-gray-200'">★</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ item.review_text }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex-shrink-0 flex items-center gap-2">
                        <button @click="toggleStatus(item)"
                                class="px-2.5 py-1 rounded-lg text-xs font-semibold transition"
                                :class="item.status === 'active'
                                    ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                    : 'bg-gray-100 text-gray-500 hover:bg-gray-200'">
                            {{ item.status === 'active' ? 'แสดงอยู่' : 'ซ่อน' }}
                        </button>
                        <button @click="openEdit(item)"
                                class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                        </button>
                        <button @click="deleteItem(item)"
                                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- ── SETTINGS TAB ──────────────────────────────────────────── -->
        <template v-else>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Section text -->
                <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                    <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">ข้อความส่วน Header</h2>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Label (เช่น TESTIMONIALS)</label>
                        <input v-model="settingsForm.label" type="text" maxlength="50"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        <p v-if="settingsErrors.label" class="text-red-500 text-xs mt-1">{{ settingsErrors.label }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Heading หลัก</label>
                        <input v-model="settingsForm.heading" type="text" maxlength="200"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        <p v-if="settingsErrors.heading" class="text-red-500 text-xs mt-1">{{ settingsErrors.heading }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">ข้อความ Overlay (บน featured image)</label>
                        <input v-model="settingsForm.overlay_text" type="text" maxlength="150"
                               placeholder="ขอบคุณทุกกำลังใจที่ให้กับทีมเรา"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">ข้อความปุ่ม CTA</label>
                            <input v-model="settingsForm.cta_text" type="text" maxlength="50"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">URL ปุ่ม CTA</label>
                            <input v-model="settingsForm.cta_url" type="text"
                                   placeholder="https://..."
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        </div>
                    </div>
                </div>

                <!-- Bg color + featured image -->
                <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                    <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">สี &amp; รูปภาพ</h2>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">สีพื้นหลัง Section</label>
                        <div class="flex items-center gap-3">
                            <input type="color" v-model="settingsForm.bg_color"
                                   class="w-10 h-10 rounded-lg border border-gray-300 cursor-pointer p-0.5"/>
                            <input v-model="settingsForm.bg_color" type="text" maxlength="7"
                                   class="w-28 border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            <div class="flex-1 h-10 rounded-xl border border-gray-200" :style="`background: ${settingsForm.bg_color}`"></div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Featured Image (รูปคนฝั่งซ้าย)</label>
                        <div v-if="featuredPreview" class="relative rounded-xl overflow-hidden mb-2 aspect-[3/4] max-w-[180px]">
                            <img :src="featuredPreview" class="w-full h-full object-cover"/>
                            <button type="button" @click="featuredPreview = null; settingsForm.featured_image = null"
                                    class="absolute top-1 right-1 bg-black/50 text-white rounded-full p-1 hover:bg-black/70 transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div @click="featuredInput.click()"
                             class="flex items-center justify-center border-2 border-dashed border-gray-300 rounded-xl py-5 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                            <div class="text-center">
                                <svg class="mx-auto w-6 h-6 text-gray-400 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                                <p class="text-xs text-gray-500">{{ featuredPreview ? 'เปลี่ยนรูป' : 'คลิกเพื่อเลือกรูป' }}</p>
                            </div>
                        </div>
                        <input ref="featuredInput" type="file" accept="image/*" class="hidden" @change="onFeaturedChange"/>
                    </div>
                </div>

                <!-- Preview -->
                <div class="lg:col-span-2 rounded-2xl overflow-hidden" :style="`background: ${settingsForm.bg_color}`">
                    <div class="p-8">
                        <p class="text-xs font-bold tracking-widest uppercase text-white/60 mb-2">{{ settingsForm.label }}</p>
                        <h2 class="text-3xl font-bold text-white font-kanit">{{ settingsForm.heading }}</h2>
                        <p class="text-white/50 text-sm mt-2">— preview สี Section —</p>
                    </div>
                </div>

                <!-- Save button -->
                <div class="lg:col-span-2 flex justify-end">
                    <button @click="submitSettings" :disabled="settingsProcessing"
                            class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white font-semibold px-8 py-2.5 rounded-xl transition text-sm">
                        {{ settingsProcessing ? 'กำลังบันทึก...' : 'บันทึกการตั้งค่า' }}
                    </button>
                </div>
            </div>
        </template>

        <!-- ── SLIDE-OVER PANEL ───────────────────────────────────────── -->
        <Teleport to="body">
            <Transition enter-from-class="translate-x-full" leave-to-class="translate-x-full"
                        enter-active-class="transition duration-300" leave-active-class="transition duration-300">
                <div v-if="panelOpen" class="fixed inset-0 z-50 flex justify-end">
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closePanel"></div>
                    <div class="relative w-full max-w-md bg-white h-full overflow-y-auto shadow-2xl flex flex-col">

                        <!-- Panel header -->
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                            <h2 class="font-bold text-gray-900">{{ editTarget ? 'แก้ไขรีวิว' : 'เพิ่มรีวิวใหม่' }}</h2>
                            <button @click="closePanel" class="text-gray-400 hover:text-gray-700 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Panel body -->
                        <div class="flex-1 px-6 py-5 space-y-5">

                            <!-- Avatar upload -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-2">รูปโปรไฟล์</label>
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 flex items-center justify-center text-white text-2xl font-bold cursor-pointer hover:opacity-80 transition"
                                         :style="avatarPreview ? '' : `background: ${form.avatar_color}`"
                                         @click="avatarInput.click()">
                                        <img v-if="avatarPreview" :src="avatarPreview" class="w-full h-full object-cover"/>
                                        <span v-else>{{ initial(form.customer_name) || '?' }}</span>
                                    </div>
                                    <div class="space-y-1.5">
                                        <button type="button" @click="avatarInput.click()"
                                                class="text-xs text-blue-600 hover:underline">เลือกรูป</button>
                                        <div class="flex flex-wrap gap-1.5">
                                            <button v-for="c in AVATAR_COLORS" :key="c" type="button"
                                                    @click="form.avatar_color = c"
                                                    class="w-6 h-6 rounded-full ring-offset-1 transition"
                                                    :class="form.avatar_color === c ? 'ring-2 ring-gray-400 scale-110' : 'hover:scale-110'"
                                                    :style="`background: ${c}`"/>
                                        </div>
                                    </div>
                                    <input ref="avatarInput" type="file" accept="image/*" class="hidden" @change="onAvatarChange"/>
                                </div>
                            </div>

                            <!-- Name + Title -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">ชื่อผู้รีวิว *</label>
                                <input v-model="form.customer_name" type="text" maxlength="100"
                                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       :class="errors.customer_name ? 'border-red-400' : 'border-gray-300'"/>
                                <p v-if="errors.customer_name" class="text-red-500 text-xs mt-1">{{ errors.customer_name }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">ตำแหน่ง / บริษัท</label>
                                <input v-model="form.customer_title" type="text" maxlength="150"
                                       placeholder="เช่น เจ้าของร้าน, CEO at Company"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>

                            <!-- Rating -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-2">คะแนน</label>
                                <div class="flex gap-1">
                                    <button v-for="s in 5" :key="s" type="button"
                                            @click="form.rating = s"
                                            class="text-2xl transition hover:scale-110"
                                            :class="s <= form.rating ? 'text-amber-400' : 'text-gray-200'">★</button>
                                </div>
                            </div>

                            <!-- Review text -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">ข้อความรีวิว *</label>
                                <textarea v-model="form.review_text" rows="4"
                                          class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                                          :class="errors.review_text ? 'border-red-400' : 'border-gray-300'"/>
                                <p v-if="errors.review_text" class="text-red-500 text-xs mt-1">{{ errors.review_text }}</p>
                            </div>

                            <!-- Source -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">แหล่งรีวิว</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button v-for="src in ['direct','google','facebook']" :key="src"
                                            type="button"
                                            @click="form.source = src"
                                            class="py-2 rounded-lg border text-xs font-medium transition capitalize"
                                            :class="form.source === src
                                                ? 'bg-blue-600 text-white border-blue-600'
                                                : 'border-gray-300 text-gray-600 hover:border-blue-400'">
                                        {{ src === 'direct' ? 'ส่งตรง' : src }}
                                    </button>
                                </div>
                            </div>

                            <div v-if="form.source !== 'direct'">
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">URL รีวิว (ลิงก์ต้นทาง)</label>
                                <input v-model="form.source_url" type="text" placeholder="https://..."
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>

                            <!-- Status -->
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <div class="relative">
                                        <input type="checkbox"
                                               :checked="form.status === 'active'"
                                               @change="form.status = $event.target.checked ? 'active' : 'inactive'"
                                               class="sr-only peer"/>
                                        <div class="w-10 h-6 bg-gray-200 peer-checked:bg-blue-600 rounded-full transition"></div>
                                        <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-4"></div>
                                    </div>
                                    <span class="text-sm text-gray-700">แสดงบนหน้าเว็บ</span>
                                </label>
                            </div>
                        </div>

                        <!-- Panel footer -->
                        <div class="px-6 py-4 border-t border-gray-200 flex gap-3">
                            <button type="button" @click="closePanel"
                                    class="flex-1 py-2.5 rounded-xl border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                ยกเลิก
                            </button>
                            <button type="button" @click="submitPanel" :disabled="processing"
                                    class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-sm font-semibold text-white transition">
                                {{ processing ? 'กำลังบันทึก...' : (editTarget ? 'บันทึก' : 'เพิ่มรีวิว') }}
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

    </AdminLayout>
</template>
