<script setup>
import { ref, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    services: { type: Array, default: () => [] },
});

// ─── Icon options ─────────────────────────────────────────────────────────────
const ICONS = [
    { value: 'globe',    label: 'เว็บไซต์' },
    { value: 'cart',     label: 'อีคอมเมิร์ซ' },
    { value: 'chart',    label: 'ดิจิทัล/SEO' },
    { value: 'code',     label: 'Development' },
    { value: 'design',   label: 'ออกแบบ' },
    { value: 'rocket',   label: 'เปิดตัว' },
    { value: 'shield',   label: 'ความปลอดภัย' },
    { value: 'support',  label: 'ซัพพอร์ต' },
];

const ICON_PATHS = {
    globe:   'M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418',
    cart:    'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z',
    chart:   'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z',
    code:    'M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5',
    design:  'M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42',
    rocket:  'M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z',
    shield:  'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z',
    support: 'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z',
};

// ─── Drag sort ────────────────────────────────────────────────────────────────
const list       = ref([...props.services]);
const dragIndex  = ref(null);

function onDragStart(i) { dragIndex.value = i; }
function onDragOver(e, i) {
    e.preventDefault();
    if (dragIndex.value === null || dragIndex.value === i) return;
    const moved = list.value.splice(dragIndex.value, 1)[0];
    list.value.splice(i, 0, moved);
    dragIndex.value = i;
}
function onDrop() {
    router.post(route('admin.services.reorder'), {
        ids: list.value.map(s => s.id),
    }, { preserveScroll: true });
    dragIndex.value = null;
}

// ─── Slide-over panel ─────────────────────────────────────────────────────────
const panelOpen    = ref(false);
const editingId    = ref(null);
const imagePreview = ref(null);

const form = useForm({
    title:       '',
    subtitle:    '',
    description: '',
    icon_name:   'globe',
    cta_text:    'ดูรายละเอียด',
    cta_url:     '',
    badge_text:  '',
    badge_color: '#7c1d1d',
    status:      'active',
    image:       null,
});

function openAdd() {
    editingId.value = null;
    imagePreview.value = null;
    form.reset();
    form.status = 'active';
    form.icon_name = 'globe';
    form.badge_color = '#7c1d1d';
    form.cta_text = 'ดูรายละเอียด';
    panelOpen.value = true;
}

function openEdit(s) {
    editingId.value = s.id;
    imagePreview.value = s.image_url ?? null;
    form.title       = s.title;
    form.subtitle    = s.subtitle ?? '';
    form.description = s.description ?? '';
    form.icon_name   = s.icon_name ?? 'globe';
    form.cta_text    = s.cta_text ?? 'ดูรายละเอียด';
    form.cta_url     = s.cta_url ?? '';
    form.badge_text  = s.badge_text ?? '';
    form.badge_color = s.badge_color ?? '#7c1d1d';
    form.status      = s.status;
    form.image       = null;
    panelOpen.value  = true;
}

function closePanel() {
    panelOpen.value = false;
    form.reset();
    imagePreview.value = null;
}

function pickImage(e) {
    const file = e.target.files[0];
    if (!file) return;
    form.image = file;
    imagePreview.value = URL.createObjectURL(file);
}

function submit() {
    if (editingId.value) {
        form.post(route('admin.services.update', editingId.value), {
            preserveScroll: true,
            onSuccess: () => {
                closePanel();
                list.value = [...props.services];
            },
        });
    } else {
        form.post(route('admin.services.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closePanel();
                list.value = [...props.services];
            },
        });
    }
}

function toggleStatus(s) {
    router.post(route('admin.services.toggle', s.id), {}, { preserveScroll: true });
}

function destroy(s) {
    if (!confirm(`ลบบริการ "${s.title}" ใช่ไหม?`)) return;
    router.delete(route('admin.services.destroy', s.id), { preserveScroll: true });
}
</script>

<template>
    <AdminLayout title="บริการของเรา">

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-semibold text-gray-900 font-kanit">บริการของเรา</h1>
                <p class="text-sm text-gray-500 mt-0.5">จัดการ service cards ที่แสดงในหน้าแรก</p>
            </div>
            <button @click="openAdd"
                    class="flex items-center gap-2 px-4 py-2 bg-red-800 text-white text-sm font-medium rounded-lg hover:bg-red-900 transition font-kanit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                เพิ่มบริการ
            </button>
        </div>

        <!-- Empty state -->
        <div v-if="!list.length" class="text-center py-16 text-gray-400 font-kanit">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z"/>
            </svg>
            ยังไม่มีบริการ กด "เพิ่มบริการ" เพื่อเริ่มต้น
        </div>

        <!-- Service cards grid -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="(s, i) in list" :key="s.id"
                 draggable="true"
                 @dragstart="onDragStart(i)"
                 @dragover="onDragOver($event, i)"
                 @drop="onDrop"
                 class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden cursor-grab hover:shadow-md transition group">

                <!-- Cover image -->
                <div class="relative h-44 bg-gray-100">
                    <img v-if="s.image_url" :src="s.image_url" :alt="s.title"
                         class="w-full h-full object-cover"/>
                    <div v-else class="w-full h-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" :d="ICON_PATHS[s.icon_name] ?? ICON_PATHS.globe"/>
                        </svg>
                    </div>
                    <!-- Badge -->
                    <span v-if="s.badge_text"
                          class="absolute top-2 left-2 px-2 py-0.5 text-xs font-semibold text-white rounded-full font-kanit"
                          :style="`background:${s.badge_color}`">{{ s.badge_text }}</span>
                    <!-- Status -->
                    <span class="absolute top-2 right-2 px-2 py-0.5 text-xs font-medium rounded-full font-kanit"
                          :class="s.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'">
                        {{ s.status === 'active' ? 'แสดง' : 'ซ่อน' }}
                    </span>
                    <!-- drag handle overlay -->
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition flex items-center justify-center">
                        <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-70" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8.5 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm7 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm-7 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm7 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm-7 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm7 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/>
                        </svg>
                    </div>
                </div>

                <!-- Card body -->
                <div class="p-4">
                    <div class="flex items-start gap-2 mb-1">
                        <svg class="w-5 h-5 text-red-800 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" :d="ICON_PATHS[s.icon_name] ?? ICON_PATHS.globe"/>
                        </svg>
                        <h3 class="font-semibold text-gray-900 font-kanit text-sm leading-snug">{{ s.title }}</h3>
                    </div>
                    <p v-if="s.subtitle" class="text-xs text-gray-500 font-kanit mb-3 line-clamp-2">{{ s.subtitle }}</p>
                    <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100">
                        <button @click="toggleStatus(s)"
                                class="flex-1 text-xs py-1.5 rounded-md border font-kanit transition"
                                :class="s.status === 'active' ? 'border-amber-200 text-amber-700 hover:bg-amber-50' : 'border-green-200 text-green-700 hover:bg-green-50'">
                            {{ s.status === 'active' ? 'ซ่อน' : 'แสดง' }}
                        </button>
                        <button @click="openEdit(s)"
                                class="flex-1 text-xs py-1.5 rounded-md border border-blue-200 text-blue-700 hover:bg-blue-50 font-kanit transition">
                            แก้ไข
                        </button>
                        <button @click="destroy(s)"
                                class="flex-1 text-xs py-1.5 rounded-md border border-red-200 text-red-700 hover:bg-red-50 font-kanit transition">
                            ลบ
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Slide-over panel ──────────────────────────────────────────────── -->
        <Teleport to="body">
            <Transition name="slide-over">
                <div v-if="panelOpen" class="fixed inset-0 z-50 flex">
                    <div class="flex-1 bg-black/40" @click="closePanel"/>
                    <div class="w-full max-w-lg bg-white h-full overflow-y-auto shadow-xl flex flex-col">

                        <!-- Panel header -->
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                            <h2 class="text-base font-semibold text-gray-900 font-kanit">
                                {{ editingId ? 'แก้ไขบริการ' : 'เพิ่มบริการใหม่' }}
                            </h2>
                            <button @click="closePanel" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Panel body -->
                        <form @submit.prevent="submit" class="flex-1 p-6 space-y-5">

                            <!-- Cover image -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 font-kanit mb-2">ภาพหน้าปก</label>
                                <div class="relative h-40 bg-gray-100 rounded-lg overflow-hidden border-2 border-dashed border-gray-300 hover:border-red-400 transition cursor-pointer"
                                     @click="$refs.imgInput.click()">
                                    <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover"/>
                                    <div v-else class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                                        </svg>
                                        <span class="text-xs font-kanit">คลิกเพื่ออัปโหลดรูป (JPG/PNG/WebP)</span>
                                    </div>
                                    <div v-if="imagePreview" class="absolute inset-0 bg-black/0 hover:bg-black/20 transition flex items-center justify-center">
                                        <span class="opacity-0 hover:opacity-100 text-white text-xs font-kanit bg-black/60 px-2 py-1 rounded">เปลี่ยนรูป</span>
                                    </div>
                                </div>
                                <input ref="imgInput" type="file" accept="image/*" class="hidden" @change="pickImage"/>
                            </div>

                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 font-kanit mb-1">ชื่อบริการ <span class="text-red-500">*</span></label>
                                <input v-model="form.title" type="text" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-kanit focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none"
                                       placeholder="เช่น ออกแบบเว็บไซต์ธุรกิจ"/>
                                <p v-if="form.errors.title" class="mt-1 text-xs text-red-500">{{ form.errors.title }}</p>
                            </div>

                            <!-- Subtitle -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 font-kanit mb-1">คำอธิบายสั้น</label>
                                <input v-model="form.subtitle" type="text"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-kanit focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none"
                                       placeholder="แสดงใต้ชื่อบริการ"/>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 font-kanit mb-1">รายละเอียด</label>
                                <textarea v-model="form.description" rows="3"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-kanit focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none resize-none"
                                          placeholder="รายละเอียดเพิ่มเติมของบริการนี้"/>
                            </div>

                            <!-- Icon -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 font-kanit mb-2">ไอคอน</label>
                                <div class="grid grid-cols-4 gap-2">
                                    <button v-for="icon in ICONS" :key="icon.value"
                                            type="button"
                                            @click="form.icon_name = icon.value"
                                            class="flex flex-col items-center gap-1 p-2 rounded-lg border-2 transition text-xs font-kanit"
                                            :class="form.icon_name === icon.value ? 'border-red-600 bg-red-50 text-red-800' : 'border-gray-200 text-gray-500 hover:border-gray-300'">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" :d="ICON_PATHS[icon.value]"/>
                                        </svg>
                                        {{ icon.label }}
                                    </button>
                                </div>
                            </div>

                            <!-- CTA -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 font-kanit mb-1">ข้อความปุ่ม</label>
                                    <input v-model="form.cta_text" type="text"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-kanit focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none"
                                           placeholder="ดูรายละเอียด"/>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 font-kanit mb-1">ลิงค์ปุ่ม</label>
                                    <input v-model="form.cta_url" type="text"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-kanit focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none"
                                           placeholder="/packages"/>
                                </div>
                            </div>

                            <!-- Badge -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 font-kanit mb-1">ข้อความ Badge</label>
                                    <input v-model="form.badge_text" type="text"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-kanit focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none"
                                           placeholder="เช่น ยอดนิยม, ใหม่"/>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 font-kanit mb-1">สี Badge</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" v-model="form.badge_color"
                                               class="w-10 h-9 rounded border border-gray-300 cursor-pointer p-0.5"/>
                                        <input v-model="form.badge_color" type="text"
                                               class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm font-kanit focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none"/>
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="flex items-center gap-3">
                                <button type="button"
                                        @click="form.status = form.status === 'active' ? 'inactive' : 'active'"
                                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
                                        :class="form.status === 'active' ? 'bg-green-500' : 'bg-gray-300'">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                                          :class="form.status === 'active' ? 'translate-x-6' : 'translate-x-1'"/>
                                </button>
                                <span class="text-sm font-kanit text-gray-700">
                                    {{ form.status === 'active' ? 'แสดงบนหน้าเว็บ' : 'ซ่อนจากหน้าเว็บ' }}
                                </span>
                            </div>
                        </form>

                        <!-- Panel footer -->
                        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-end gap-3">
                            <button @click="closePanel" type="button"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-kanit">
                                ยกเลิก
                            </button>
                            <button @click="submit"
                                    :disabled="form.processing"
                                    class="px-5 py-2 bg-red-800 text-white text-sm font-medium rounded-lg hover:bg-red-900 transition disabled:opacity-50 font-kanit">
                                {{ form.processing ? 'กำลังบันทึก...' : (editingId ? 'บันทึกการแก้ไข' : 'เพิ่มบริการ') }}
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

    </AdminLayout>
</template>

<style scoped>
.slide-over-enter-active, .slide-over-leave-active { transition: opacity 0.25s; }
.slide-over-enter-from, .slide-over-leave-to { opacity: 0; }
.slide-over-enter-active > div:last-child,
.slide-over-leave-active > div:last-child { transition: transform 0.25s; }
.slide-over-enter-from > div:last-child,
.slide-over-leave-to > div:last-child { transform: translateX(100%); }
</style>
