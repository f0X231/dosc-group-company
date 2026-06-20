<script setup>
import { ref, computed, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    pages: { type: Array, default: () => [] },
});

// Flash messages
const flash = computed(() => router.page?.props?.flash ?? {});

// ─── Slide-over state ────────────────────────────────────────────────────────
const open        = ref(false);
const selected    = ref(null);
const showAdvanced = ref(false);
const ogPreview   = ref(null);

const form = useForm({
    meta_title:       '',
    meta_description: '',
    og_title:         '',
    og_description:   '',
    og_image:         null,
    robots:           'index,follow',
    canonical_url:    '',
    schema_json:      '',
});

function openEdit(page) {
    selected.value    = page;
    showAdvanced.value = false;
    ogPreview.value   = page.og_image_url || null;
    form.reset();
    form.meta_title       = page.meta_title       ?? '';
    form.meta_description = page.meta_description ?? '';
    form.og_title         = page.og_title         ?? '';
    form.og_description   = page.og_description   ?? '';
    form.og_image         = null;
    form.robots           = page.robots            ?? 'index,follow';
    form.canonical_url    = page.canonical_url     ?? '';
    form.schema_json      = page.schema_json       ?? '';
    open.value = true;
}

function closePanel() {
    open.value = false;
    selected.value = null;
}

function onOgImageChange(e) {
    const file = e.target.files[0];
    if (!file) return;
    form.og_image = file;
    ogPreview.value = URL.createObjectURL(file);
}

function removeOgImage() {
    if (!selected.value?.og_image_url) {
        form.og_image = null;
        ogPreview.value = null;
        return;
    }
    router.delete(route('admin.seo.og-image.delete', selected.value.id), {
        onSuccess: () => { ogPreview.value = null; },
    });
}

function save() {
    form.post(route('admin.seo.update', selected.value.id), {
        onSuccess: () => closePanel(),
    });
}

// ─── Char counter helpers ─────────────────────────────────────────────────────
function titleColor(len) {
    if (len === 0) return 'text-gray-400';
    if (len <= 55)  return 'text-green-600';
    if (len <= 65)  return 'text-amber-500';
    return 'text-red-500';
}
function descColor(len) {
    if (len === 0)   return 'text-gray-400';
    if (len <= 145)  return 'text-green-600';
    if (len <= 165)  return 'text-amber-500';
    return 'text-red-500';
}

// ─── Status helpers ───────────────────────────────────────────────────────────
function seoScore(page) {
    let score = 0;
    if (page.meta_title)       score++;
    if (page.meta_description) score++;
    if (page.og_image_url)     score++;
    return score;
}

const ROBOTS_OPTIONS = [
    { value: 'index,follow',   label: 'index, follow — อนุญาตให้ Google จัดเก็บและติดตามลิงก์ (แนะนำ)' },
    { value: 'noindex,follow', label: 'noindex, follow — ไม่แสดงในผลค้นหา แต่ติดตามลิงก์ได้' },
    { value: 'noindex,nofollow', label: 'noindex, nofollow — ซ่อนทั้งหมดจาก Google' },
];
</script>

<template>
    <AdminLayout>
        <div class="p-6 lg:p-8 max-w-6xl mx-auto">

            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">จัดการ SEO</h1>
                <p class="mt-1 text-sm text-gray-500">
                    ตั้งค่า Meta Title, Meta Description, Open Graph และ Structured Data สำหรับแต่ละหน้า<br/>
                    <span class="text-amber-600 font-medium">หมายเหตุ:</span> เนื่องจากไม่ได้ใช้ Server-Side Rendering, tag เหล่านี้จะถูก inject โดย JavaScript — Googlebot รองรับได้ดี แต่ social crawler (Facebook/Line) อาจไม่เห็น OG tags
                </p>
            </div>

            <!-- Flash -->
            <div v-if="$page.props.flash?.success" class="mb-6 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/></svg>
                {{ $page.props.flash.success }}
            </div>

            <!-- Page Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="page in pages" :key="page.id"
                     class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">

                    <!-- Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-900 text-sm">{{ page.page_label }}</h3>
                            <p class="text-xs text-gray-400 font-mono mt-0.5">{{ page.page_key }}</p>
                        </div>
                        <!-- Score badge -->
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full"
                                  :class="{
                                      'bg-green-100 text-green-700':  seoScore(page) === 3,
                                      'bg-amber-100 text-amber-700':  seoScore(page) === 2,
                                      'bg-orange-100 text-orange-700': seoScore(page) === 1,
                                      'bg-red-100 text-red-600':      seoScore(page) === 0,
                                  }">
                                {{ seoScore(page) }}/3
                            </span>
                        </div>
                    </div>

                    <!-- Status indicators -->
                    <div class="space-y-1.5 mb-4">
                        <div class="flex items-center gap-2 text-xs">
                            <span :class="page.meta_title ? 'text-green-500' : 'text-gray-300'">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path v-if="page.meta_title" fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
                                    <path v-else fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                            <span :class="page.meta_title ? 'text-gray-700' : 'text-gray-400'">Meta Title</span>
                            <span v-if="page.meta_title" class="text-gray-400 truncate max-w-[140px]">{{ page.meta_title }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <span :class="page.meta_description ? 'text-green-500' : 'text-gray-300'">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path v-if="page.meta_description" fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
                                    <path v-else fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                            <span :class="page.meta_description ? 'text-gray-700' : 'text-gray-400'">Meta Description</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <span :class="page.og_image_url ? 'text-green-500' : 'text-gray-300'">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path v-if="page.og_image_url" fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd"/>
                                    <path v-else fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                            <span :class="page.og_image_url ? 'text-gray-700' : 'text-gray-400'">OG Image</span>
                        </div>
                    </div>

                    <!-- robots badge -->
                    <div class="mb-4">
                        <span class="text-[10px] font-mono px-1.5 py-0.5 rounded"
                              :class="page.robots === 'index,follow' ? 'bg-gray-100 text-gray-500' : 'bg-amber-100 text-amber-700'">
                            {{ page.robots }}
                        </span>
                    </div>

                    <button @click="openEdit(page)"
                            class="w-full text-center text-xs font-semibold text-red-700 hover:text-red-800 border border-red-200 hover:border-red-400 py-2 rounded-lg transition-colors">
                        แก้ไข SEO
                    </button>
                </div>
            </div>
        </div>

        <!-- ─── Slide-over Panel ──────────────────────────────────────────────── -->
        <Teleport to="body">
            <div v-if="open" class="fixed inset-0 z-50 flex">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/40" @click="closePanel"></div>

                <!-- Panel -->
                <div class="relative ml-auto w-full max-w-xl bg-white h-full flex flex-col shadow-2xl overflow-hidden">

                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <div>
                            <h2 class="font-bold text-gray-900">{{ selected?.page_label }}</h2>
                            <p class="text-xs text-gray-400 font-mono mt-0.5">{{ selected?.page_key }}</p>
                        </div>
                        <button @click="closePanel" class="p-1.5 rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-200 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Form -->
                    <div class="flex-1 overflow-y-auto px-6 py-6 space-y-6">

                        <!-- Error summary -->
                        <div v-if="Object.keys(form.errors).length" class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-700 space-y-1">
                            <p v-for="(msg, key) in form.errors" :key="key">{{ msg }}</p>
                        </div>

                        <!-- ── Meta Tags ─────────────────────────────────── -->
                        <section>
                            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-4 flex items-center gap-2">
                                <span class="w-4 h-0.5 bg-red-600 rounded"></span>
                                เมตาแท็ก (Meta Tags)
                            </h3>

                            <!-- Meta Title -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-sm font-medium text-gray-700">Meta Title</label>
                                    <span class="text-xs font-mono" :class="titleColor(form.meta_title.length)">
                                        {{ form.meta_title.length }}/60
                                    </span>
                                </div>
                                <input v-model="form.meta_title" type="text" maxlength="100"
                                       placeholder="ชื่อหน้าที่แสดงใน Google (แนะนำ ≤ 60 ตัวอักษร)"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"/>
                                <p class="mt-1 text-xs text-gray-400">จะแสดงในรูปแบบ: <span class="text-gray-600 font-medium">{{ form.meta_title || '...' }} | DOSC Group</span></p>
                            </div>

                            <!-- Meta Description -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-sm font-medium text-gray-700">Meta Description</label>
                                    <span class="text-xs font-mono" :class="descColor(form.meta_description.length)">
                                        {{ form.meta_description.length }}/160
                                    </span>
                                </div>
                                <textarea v-model="form.meta_description" rows="3" maxlength="300"
                                          placeholder="คำอธิบายหน้า ที่แสดงใต้ชื่อใน Google (แนะนำ ≤ 160 ตัวอักษร)"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"></textarea>
                            </div>
                        </section>

                        <!-- ── Open Graph ────────────────────────────────── -->
                        <section>
                            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-4 flex items-center gap-2">
                                <span class="w-4 h-0.5 bg-red-600 rounded"></span>
                                Open Graph (Social Sharing)
                            </h3>

                            <!-- OG Title -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">OG Title <span class="text-gray-400 font-normal">(ถ้าว่าง ใช้ Meta Title)</span></label>
                                <input v-model="form.og_title" type="text" maxlength="100"
                                       placeholder="ชื่อที่แสดงตอนแชร์บน Facebook / LINE"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"/>
                            </div>

                            <!-- OG Description -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">OG Description <span class="text-gray-400 font-normal">(ถ้าว่าง ใช้ Meta Description)</span></label>
                                <textarea v-model="form.og_description" rows="2" maxlength="300"
                                          placeholder="คำอธิบายที่แสดงตอนแชร์บน social media"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"></textarea>
                            </div>

                            <!-- OG Image -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">OG Image <span class="text-gray-400 font-normal">(แนะนำ 1200×630 px)</span></label>
                                <div v-if="ogPreview" class="mb-2 relative group w-full aspect-[1200/630] rounded-lg overflow-hidden bg-gray-100 border border-gray-200">
                                    <img :src="ogPreview" class="w-full h-full object-cover"/>
                                    <button @click="removeOgImage"
                                            class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <label class="flex items-center gap-2 cursor-pointer border-2 border-dashed border-gray-300 hover:border-red-400 rounded-lg px-4 py-3 text-sm text-gray-500 hover:text-red-600 transition">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                                    {{ ogPreview ? 'เปลี่ยนรูป' : 'อัปโหลด OG Image' }}
                                    <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="onOgImageChange"/>
                                </label>
                            </div>
                        </section>

                        <!-- ── Robots ─────────────────────────────────────── -->
                        <section>
                            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-4 flex items-center gap-2">
                                <span class="w-4 h-0.5 bg-red-600 rounded"></span>
                                การจัดทำดัชนี (Robots)
                            </h3>
                            <div class="space-y-2">
                                <label v-for="opt in ROBOTS_OPTIONS" :key="opt.value"
                                       class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border transition"
                                       :class="form.robots === opt.value ? 'border-red-400 bg-red-50' : 'border-gray-200 hover:border-gray-300'">
                                    <input type="radio" :value="opt.value" v-model="form.robots" class="mt-0.5 accent-red-600"/>
                                    <span class="text-sm text-gray-700">{{ opt.label }}</span>
                                </label>
                            </div>
                        </section>

                        <!-- ── Advanced (collapsible) ─────────────────────── -->
                        <section>
                            <button type="button" @click="showAdvanced = !showAdvanced"
                                    class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-gray-500 hover:text-gray-700 transition">
                                <svg class="w-3.5 h-3.5 transition-transform" :class="showAdvanced ? 'rotate-90' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
                                ขั้นสูง (Canonical URL &amp; JSON-LD)
                            </button>

                            <div v-show="showAdvanced" class="mt-4 space-y-4">
                                <!-- Canonical URL -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Canonical URL <span class="text-gray-400 font-normal">(ปล่อยว่างเพื่อใช้ URL ปัจจุบัน)</span></label>
                                    <input v-model="form.canonical_url" type="url"
                                           placeholder="https://example.com/page"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent font-mono"/>
                                </div>

                                <!-- Schema JSON-LD -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Schema JSON-LD
                                        <a href="https://schema.org" target="_blank" class="text-red-600 text-xs ml-1 hover:underline">schema.org →</a>
                                    </label>
                                    <textarea v-model="form.schema_json" rows="8"
                                              placeholder='{"@context":"https://schema.org","@type":"LocalBusiness","name":"DOSC Group",...}'
                                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                                              :class="form.errors.schema_json ? 'border-red-400' : ''"></textarea>
                                    <p v-if="form.errors.schema_json" class="mt-1 text-xs text-red-500">{{ form.errors.schema_json }}</p>
                                    <p class="mt-1 text-xs text-gray-400">Structured data สำหรับ Google Rich Results — ต้องเป็น JSON ที่ valid เท่านั้น</p>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- Footer -->
                    <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex items-center justify-between gap-3">
                        <button @click="closePanel" type="button"
                                class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                            ยกเลิก
                        </button>
                        <button @click="save" type="button"
                                :disabled="form.processing"
                                class="px-6 py-2 text-sm font-semibold bg-red-700 hover:bg-red-800 text-white rounded-lg transition disabled:opacity-60">
                            {{ form.processing ? 'กำลังบันทึก...' : 'บันทึก SEO' }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>
