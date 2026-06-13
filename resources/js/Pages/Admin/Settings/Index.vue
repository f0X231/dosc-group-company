<script setup>
import { ref, reactive, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SocialIcon from '@/Components/SocialIcon.vue';

const props = defineProps({
    site:        { type: Object, default: () => ({}) },
    socialLinks: { type: Array,  default: () => [] },
});

// ─── Tabs ─────────────────────────────────────────────────────────────────────
const activeTab = ref('general');

// ─── Flash message ────────────────────────────────────────────────────────────
import { usePage } from '@inertiajs/vue3';
const page = usePage();

// ─── General form ──────────────────────────────────────────────────────────
const gProcessing = ref(false);
const gErrors     = ref({});
const g = reactive({
    site_name:        props.site.site_name        ?? '',
    tagline:          props.site.tagline          ?? '',
    meta_description: props.site.meta_description ?? '',
    logo:             null,
    logo_dark:        null,
    favicon:          null,
    default_og_image: null,
});
const logoPreview   = ref(props.site.logo_url            ?? null);
const logoDkPreview = ref(props.site.logo_dark_url       ?? null);
const faviconPreview = ref(props.site.favicon_url        ?? null);
const ogPreview     = ref(props.site.default_og_image_url ?? null);

function onImgChange(e, field, previewRef) {
    const f = e.target.files?.[0];
    if (!f) return;
    g[field]          = f;
    previewRef.value  = URL.createObjectURL(f);
}

function submitGeneral() {
    gErrors.value     = {};
    gProcessing.value = true;
    const fd = new FormData();
    fd.append('site_name',        g.site_name);
    fd.append('tagline',          g.tagline);
    fd.append('meta_description', g.meta_description);
    if (g.logo)             fd.append('logo',             g.logo);
    if (g.logo_dark)        fd.append('logo_dark',        g.logo_dark);
    if (g.favicon)          fd.append('favicon',          g.favicon);
    if (g.default_og_image) fd.append('default_og_image', g.default_og_image);
    router.post(route('admin.settings.general'), fd, {
        forceFormData:  true,
        onError:        (e) => { gErrors.value = e; },
        onFinish:       () => { gProcessing.value = false; },
        preserveScroll: true,
    });
}

// ─── Contact form ─────────────────────────────────────────────────────────────
const cProcessing = ref(false);
const cErrors     = ref({});
const c = reactive({
    phone:            props.site.phone            ?? '',
    phone_secondary:  props.site.phone_secondary  ?? '',
    email:            props.site.email            ?? '',
    email_secondary:  props.site.email_secondary  ?? '',
    address:          props.site.address          ?? '',
    google_map_url:   props.site.google_map_url   ?? '',
    google_map_embed: props.site.google_map_embed ?? '',
});

function submitContact() {
    cErrors.value     = {};
    cProcessing.value = true;
    router.post(route('admin.settings.contact'), { ...c }, {
        onError:        (e) => { cErrors.value = e; },
        onFinish:       () => { cProcessing.value = false; },
        preserveScroll: true,
    });
}

// ─── Social links ─────────────────────────────────────────────────────────────
const socialItems   = ref(props.socialLinks.map(l => ({ ...l, _editing: false, _processing: false })));
watch(() => props.socialLinks, (v) => {
    socialItems.value = v.map(l => ({ ...l, _editing: false, _processing: false }));
}, { deep: true });

const PLATFORM_LABELS = {
    facebook:'Facebook', instagram:'Instagram', twitter:'X (Twitter)',
    linkedin:'LinkedIn', line:'LINE', youtube:'YouTube', tiktok:'TikTok', custom:'Custom',
};

// Add custom panel
const showAddPanel   = ref(false);
const addProcessing  = ref(false);
const addErrors      = ref({});
const newLink = reactive({
    platform:   'custom',
    label:      '',
    url:        '',
    icon_type:  'builtin',
    icon_value: '',
    color:      '#000000',
    is_active:  false,
    icon_image: null,
});
const newIconPreview = ref(null);

function onNewIconChange(e) {
    const f = e.target.files?.[0];
    if (!f) return;
    newLink.icon_image = f;
    newIconPreview.value = URL.createObjectURL(f);
}

function submitAddSocial() {
    addErrors.value = {};
    addProcessing.value = true;
    const fd = new FormData();
    ['platform','label','url','icon_type','icon_value','color'].forEach(k => fd.append(k, newLink[k]));
    fd.append('is_active', newLink.is_active ? '1' : '0');
    if (newLink.icon_image) fd.append('icon_image', newLink.icon_image);
    router.post(route('admin.settings.social.store'), fd, {
        forceFormData:  true,
        onSuccess:      () => { showAddPanel.value = false; },
        onError:        (e) => { addErrors.value = e; },
        onFinish:       () => { addProcessing.value = false; },
        preserveScroll: true,
    });
}

function updateSocial(item) {
    item._processing = true;
    const fd = new FormData();
    ['label','url','icon_type','icon_value','color'].forEach(k => fd.append(k, item[k] ?? ''));
    fd.append('is_active', item.is_active ? '1' : '0');
    if (item._icon_file) fd.append('icon_image', item._icon_file);
    router.post(route('admin.settings.social.update', item.id), fd, {
        forceFormData:  true,
        onSuccess:      () => { item._editing = false; },
        onFinish:       () => { item._processing = false; },
        preserveScroll: true,
    });
}

function deleteSocial(item) {
    if (!confirm(`ลบ "${item.label}" ?`)) return;
    router.delete(route('admin.settings.social.destroy', item.id), { preserveScroll: true });
}

function onItemIconChange(e, item) {
    const f = e.target.files?.[0];
    if (!f) return;
    item._icon_file     = f;
    item._icon_preview  = URL.createObjectURL(f);
}

// Drag-to-reorder social
const draggingId = ref(null);
function onDragStart(item) { draggingId.value = item.id; }
function onDragOver(e, item) {
    e.preventDefault();
    if (draggingId.value === item.id) return;
    const from = socialItems.value.findIndex(i => i.id === draggingId.value);
    const to   = socialItems.value.findIndex(i => i.id === item.id);
    if (from < 0 || to < 0) return;
    const copy = [...socialItems.value];
    copy.splice(to, 0, copy.splice(from, 1)[0]);
    socialItems.value = copy;
}
function onDrop() {
    draggingId.value = null;
    router.post(route('admin.settings.social.reorder'), {
        ids: socialItems.value.map(i => i.id),
    }, { preserveScroll: true });
}

// File input refs for logo uploads
const logoInput   = ref(null);
const logoDkInput = ref(null);
const faviconInput = ref(null);
const ogInput     = ref(null);
</script>

<template>
    <Head title="Admin — ตั้งค่าเว็บไซต์" />
    <AdminLayout>

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">ตั้งค่าเว็บไซต์</h1>
        </div>

        <!-- Flash success -->
        <div v-if="page.props.flash?.success"
             class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
            {{ page.props.flash.success }}
        </div>

        <!-- Tabs -->
        <div class="flex gap-1 bg-gray-100 p-1 rounded-xl w-fit mb-6">
            <button v-for="tab in [
                { key: 'general', label: 'ข้อมูลทั่วไป' },
                { key: 'contact', label: 'ข้อมูลติดต่อ' },
                { key: 'social',  label: 'Social Networks' },
            ]" :key="tab.key"
                    @click="activeTab = tab.key"
                    class="px-4 py-1.5 rounded-lg text-sm font-medium transition"
                    :class="activeTab === tab.key ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'">
                {{ tab.label }}
            </button>
        </div>

        <!-- ══════ TAB: GENERAL ══════ -->
        <template v-if="activeTab === 'general'">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Text info -->
                <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                    <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">ข้อมูลเว็บไซต์</h2>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">ชื่อเว็บไซต์ *</label>
                        <input v-model="g.site_name" type="text" maxlength="100"
                               class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               :class="gErrors.site_name ? 'border-red-400' : 'border-gray-300'"/>
                        <p v-if="gErrors.site_name" class="text-red-500 text-xs mt-1">{{ gErrors.site_name }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Tagline / คำโปรย</label>
                        <input v-model="g.tagline" type="text" maxlength="200"
                               placeholder="ทำเว็บไซต์ให้เป็นเรื่องง่ายสำหรับคุณ"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-medium text-gray-500">Meta Description (default)</label>
                            <span class="text-xs" :class="g.meta_description.length > 145 ? 'text-amber-500' : 'text-gray-400'">
                                {{ g.meta_description.length }}/160
                            </span>
                        </div>
                        <textarea v-model="g.meta_description" rows="2" maxlength="160"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"/>
                    </div>
                </div>

                <!-- Images -->
                <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                    <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">Logo &amp; Favicon</h2>

                    <!-- Logo light -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Logo (Light bg)</label>
                        <div class="flex items-center gap-3">
                            <div @click="logoInput.click()"
                                 class="flex-shrink-0 w-32 h-12 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition overflow-hidden">
                                <img v-if="logoPreview" :src="logoPreview" class="max-h-full max-w-full object-contain p-1"/>
                                <svg v-else class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                            </div>
                            <div class="text-xs text-gray-400 space-y-0.5">
                                <p>PNG / SVG / WebP</p>
                                <p>แนะนำ transparent bg</p>
                                <button v-if="logoPreview" type="button" @click="logoPreview=null; g.logo=null" class="text-red-400 hover:text-red-600">ลบรูป</button>
                            </div>
                        </div>
                        <input ref="logoInput" type="file" accept="image/*" class="hidden" @change="onImgChange($event,'logo',logoPreview)"/>
                    </div>

                    <!-- Logo dark -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Logo (Dark bg) — สำหรับ header เข้ม/footer</label>
                        <div class="flex items-center gap-3">
                            <div @click="logoDkInput.click()"
                                 class="flex-shrink-0 w-32 h-12 bg-gray-900 border-2 border-dashed border-gray-600 rounded-xl flex items-center justify-center cursor-pointer hover:border-blue-400 transition overflow-hidden">
                                <img v-if="logoDkPreview" :src="logoDkPreview" class="max-h-full max-w-full object-contain p-1"/>
                                <svg v-else class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                            </div>
                            <div class="text-xs text-gray-400">
                                <button v-if="logoDkPreview" type="button" @click="logoDkPreview=null; g.logo_dark=null" class="text-red-400 hover:text-red-600">ลบรูป</button>
                            </div>
                        </div>
                        <input ref="logoDkInput" type="file" accept="image/*" class="hidden" @change="onImgChange($event,'logo_dark',logoDkPreview)"/>
                    </div>

                    <!-- Favicon + OG -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Favicon (32×32)</label>
                            <div @click="faviconInput.click()"
                                 class="w-16 h-16 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition overflow-hidden">
                                <img v-if="faviconPreview" :src="faviconPreview" class="w-full h-full object-contain"/>
                                <svg v-else class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                            </div>
                            <input ref="faviconInput" type="file" accept="image/png,image/x-icon,image/svg+xml" class="hidden" @change="onImgChange($event,'favicon',faviconPreview)"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Default OG Image</label>
                            <div @click="ogInput.click()"
                                 class="w-full h-16 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition overflow-hidden">
                                <img v-if="ogPreview" :src="ogPreview" class="w-full h-full object-cover"/>
                                <span v-else class="text-xs text-gray-400">1200×630</span>
                            </div>
                            <input ref="ogInput" type="file" accept="image/*" class="hidden" @change="onImgChange($event,'default_og_image',ogPreview)"/>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 flex justify-end">
                    <button @click="submitGeneral" :disabled="gProcessing"
                            class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white font-semibold px-8 py-2.5 rounded-xl transition text-sm">
                        {{ gProcessing ? 'กำลังบันทึก...' : 'บันทึกข้อมูลทั่วไป' }}
                    </button>
                </div>
            </div>
        </template>

        <!-- ══════ TAB: CONTACT ══════ -->
        <template v-else-if="activeTab === 'contact'">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Phone + Email -->
                <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                    <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">โทรศัพท์ &amp; อีเมล</h2>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">เบอร์หลัก</label>
                            <input v-model="c.phone" type="text" maxlength="20" placeholder="02-xxx-xxxx"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">เบอร์สำรอง</label>
                            <input v-model="c.phone_secondary" type="text" maxlength="20"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Email หลัก</label>
                            <input v-model="c.email" type="email" maxlength="100"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   :class="cErrors.email ? 'border-red-400' : ''"/>
                            <p v-if="cErrors.email" class="text-red-500 text-xs mt-1">{{ cErrors.email }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">Email สำรอง</label>
                            <input v-model="c.email_secondary" type="email" maxlength="100"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">ที่อยู่</label>
                        <textarea v-model="c.address" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"/>
                    </div>
                </div>

                <!-- Google Maps -->
                <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                    <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">Google Maps</h2>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Google Maps URL</label>
                        <input v-model="c.google_map_url" type="text"
                               placeholder="https://maps.google.com/..."
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        <p class="text-xs text-gray-400 mt-1">ลิงก์สำหรับกดเปิดใน Google Maps</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Embed Code (iframe)</label>
                        <textarea v-model="c.google_map_embed" rows="5" spellcheck="false"
                                  placeholder='&lt;iframe src="https://www.google.com/maps/embed?..."&gt;&lt;/iframe&gt;'
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none bg-gray-50"/>
                        <p class="text-xs text-gray-400 mt-1">วาง embed code จาก Google Maps → Share → Embed a map</p>
                    </div>

                    <!-- Map preview -->
                    <div v-if="c.google_map_embed" class="rounded-xl overflow-hidden aspect-video border border-gray-200"
                         v-html="c.google_map_embed.replace('<iframe', '<iframe class=\'w-full h-full\'')" />
                </div>

                <div class="lg:col-span-2 flex justify-end">
                    <button @click="submitContact" :disabled="cProcessing"
                            class="bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white font-semibold px-8 py-2.5 rounded-xl transition text-sm">
                        {{ cProcessing ? 'กำลังบันทึก...' : 'บันทึกข้อมูลติดต่อ' }}
                    </button>
                </div>
            </div>
        </template>

        <!-- ══════ TAB: SOCIAL ══════ -->
        <template v-else>
            <div class="space-y-3">

                <!-- Social link rows -->
                <div v-for="item in socialItems" :key="item.id"
                     draggable="true"
                     @dragstart="onDragStart(item)"
                     @dragover="onDragOver($event, item)"
                     @drop="onDrop" @dragend="onDrop"
                     class="bg-white rounded-xl shadow-sm overflow-hidden transition"
                     :class="draggingId === item.id ? 'opacity-40' : ''">

                    <!-- Row summary (collapsed) -->
                    <div v-if="!item._editing" class="flex items-center gap-3 px-4 py-3">
                        <!-- Drag -->
                        <div class="text-gray-300 cursor-grab flex-shrink-0">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm6 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm6 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm6 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg>
                        </div>

                        <!-- Icon circle -->
                        <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center text-white"
                             :style="`background: ${item.color}`">
                            <SocialIcon :platform="item.platform" :icon-type="item.icon_type"
                                        :icon-value="item.icon_value" :icon-url="item.icon_url" size="md"/>
                        </div>

                        <!-- Label + URL -->
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 text-sm">{{ item.label }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ item.url || '— ยังไม่ได้ใส่ URL —' }}</p>
                        </div>

                        <!-- Status badge -->
                        <span class="flex-shrink-0 text-xs font-semibold px-2 py-0.5 rounded-full"
                              :class="item.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'">
                            {{ item.is_active ? 'แสดง' : 'ซ่อน' }}
                        </span>

                        <!-- Actions -->
                        <div class="flex-shrink-0 flex gap-1">
                            <button @click="item._editing = true"
                                    class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                            </button>
                            <button v-if="item.platform === 'custom'" @click="deleteSocial(item)"
                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Expanded edit form -->
                    <div v-else class="px-4 py-4 border-t border-gray-100 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Label</label>
                                <input v-model="item.label" type="text" maxlength="50"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">URL / ลิงก์</label>
                                <input v-model="item.url" type="text"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>
                        </div>

                        <!-- Icon settings for custom platform -->
                        <div v-if="item.platform === 'custom'" class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5">ประเภท Icon</label>
                                <div class="flex gap-2">
                                    <button v-for="t in ['builtin','emoji','image']" :key="t" type="button"
                                            @click="item.icon_type = t"
                                            class="px-3 py-1.5 rounded-lg border text-xs font-medium transition capitalize"
                                            :class="item.icon_type === t ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-600 hover:border-blue-400'">
                                        {{ t }}
                                    </button>
                                </div>
                            </div>
                            <div v-if="item.icon_type === 'emoji'">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Emoji</label>
                                <input v-model="item.icon_value" type="text" maxlength="4" placeholder="🌐"
                                       class="w-20 border border-gray-300 rounded-lg px-3 py-2 text-lg text-center focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>
                            <div v-if="item.icon_type === 'image'">
                                <label class="block text-xs font-medium text-gray-500 mb-1">อัปโหลด Icon</label>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg border border-gray-200 overflow-hidden flex items-center justify-center bg-gray-50">
                                        <img v-if="item._icon_preview || item.icon_url"
                                             :src="item._icon_preview ?? item.icon_url"
                                             class="w-full h-full object-contain"/>
                                        <svg v-else class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                                    </div>
                                    <label class="cursor-pointer text-xs text-blue-600 hover:underline">
                                        เลือกไฟล์
                                        <input type="file" accept="image/png,image/svg+xml,image/jpeg,image/webp" class="hidden"
                                               @change="onItemIconChange($event, item)"/>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Color + Toggle -->
                        <div class="flex items-center gap-4 flex-wrap">
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-medium text-gray-500">สีปุ่ม</label>
                                <input type="color" v-model="item.color"
                                       class="w-8 h-8 rounded-lg border border-gray-300 cursor-pointer p-0.5"/>
                                <input v-model="item.color" type="text" maxlength="7"
                                       class="w-20 border border-gray-300 rounded-lg px-2 py-1 text-xs font-mono focus:outline-none"/>
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <div class="relative">
                                    <input type="checkbox" v-model="item.is_active" class="sr-only peer"/>
                                    <div class="w-9 h-5 bg-gray-200 peer-checked:bg-blue-600 rounded-full transition"></div>
                                    <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-4 shadow"></div>
                                </div>
                                <span class="text-sm text-gray-700">แสดงบนหน้าเว็บ</span>
                            </label>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2 pt-1">
                            <button type="button" @click="item._editing = false"
                                    class="px-4 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50 transition">
                                ยกเลิก
                            </button>
                            <button type="button" @click="updateSocial(item)" :disabled="item._processing"
                                    class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-sm font-semibold text-white transition">
                                {{ item._processing ? 'กำลังบันทึก...' : 'บันทึก' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Add custom -->
                <div v-if="!showAddPanel"
                     class="bg-white rounded-xl shadow-sm border-2 border-dashed border-gray-200 hover:border-blue-400 transition">
                    <button @click="showAddPanel = true"
                            class="w-full flex items-center justify-center gap-2 py-4 text-sm text-gray-400 hover:text-blue-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        เพิ่ม Social Link แบบกำหนดเอง
                    </button>
                </div>

                <!-- Add panel -->
                <div v-else class="bg-white rounded-xl shadow-sm p-5 space-y-4 border border-blue-200">
                    <h3 class="font-semibold text-gray-800">เพิ่ม Social Link ใหม่</h3>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Platform key</label>
                            <input v-model="newLink.platform" type="text" maxlength="30" placeholder="เช่น threads, pantip"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Label (ชื่อแสดง) *</label>
                            <input v-model="newLink.label" type="text" maxlength="50"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   :class="addErrors.label ? 'border-red-400' : ''"/>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">URL</label>
                        <input v-model="newLink.url" type="text"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">ประเภท Icon</label>
                        <div class="flex gap-2">
                            <button v-for="t in ['builtin','emoji','image']" :key="t" type="button"
                                    @click="newLink.icon_type = t"
                                    class="px-3 py-1.5 rounded-lg border text-xs font-medium transition capitalize"
                                    :class="newLink.icon_type === t ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-600'">
                                {{ t }}
                            </button>
                        </div>
                    </div>

                    <div v-if="newLink.icon_type === 'emoji'">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Emoji</label>
                        <input v-model="newLink.icon_value" type="text" maxlength="4" placeholder="🌐"
                               class="w-20 border border-gray-300 rounded-lg px-3 py-2 text-lg text-center focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>

                    <div v-if="newLink.icon_type === 'image'">
                        <label class="block text-xs font-medium text-gray-500 mb-1">อัปโหลด Icon</label>
                        <label class="cursor-pointer inline-flex items-center gap-2 text-xs text-blue-600 hover:underline">
                            <span>{{ newLink.icon_image?.name ?? 'เลือกไฟล์' }}</span>
                            <input type="file" accept="image/png,image/svg+xml,image/jpeg,image/webp" class="hidden" @change="onNewIconChange"/>
                        </label>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-medium text-gray-500">สีปุ่ม</label>
                            <input type="color" v-model="newLink.color" class="w-8 h-8 rounded-lg border border-gray-300 cursor-pointer p-0.5"/>
                            <input v-model="newLink.color" type="text" maxlength="7"
                                   class="w-20 border border-gray-300 rounded-lg px-2 py-1 text-xs font-mono focus:outline-none"/>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <div class="relative">
                                <input type="checkbox" v-model="newLink.is_active" class="sr-only peer"/>
                                <div class="w-9 h-5 bg-gray-200 peer-checked:bg-blue-600 rounded-full transition"></div>
                                <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-4 shadow"></div>
                            </div>
                            <span class="text-sm text-gray-700">แสดงทันที</span>
                        </label>
                    </div>

                    <div class="flex gap-2 pt-1">
                        <button type="button" @click="showAddPanel = false"
                                class="px-4 py-2 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50 transition">
                            ยกเลิก
                        </button>
                        <button type="button" @click="submitAddSocial" :disabled="addProcessing"
                                class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-sm font-semibold text-white transition">
                            {{ addProcessing ? 'กำลังบันทึก...' : 'เพิ่ม' }}
                        </button>
                    </div>
                </div>
            </div>
        </template>

    </AdminLayout>
</template>
