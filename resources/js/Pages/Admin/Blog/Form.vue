<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import TipTapEditor from '@/Components/TipTapEditor.vue';

const props = defineProps({
    blog:       { type: Object, default: null },
    categories: { type: Array,  default: () => [] },
});

const isEdit    = computed(() => !!props.blog);
const draftUuid = ref(props.blog?.uuid ?? crypto.randomUUID());

// ─── Form ──────────────────────────────────────────────────────────────────────
const form = useForm({
    uuid:             draftUuid.value,
    title:            props.blog?.title            ?? '',
    excerpt:          props.blog?.excerpt          ?? '',
    content:          props.blog?.content          ?? '',
    category_id:      props.blog?.category_id      ?? '',
    tags:             props.blog?.tags             ?? [],
    status:           props.blog?.status           ?? 'draft',
    published_at:     props.blog?.published_at     ? props.blog.published_at.slice(0, 16) : '',
    is_featured:      props.blog?.is_featured      ?? false,
    meta_title:       props.blog?.meta_title       ?? '',
    meta_description: props.blog?.meta_description ?? '',
    meta_keywords:    props.blog?.meta_keywords    ?? '',
    canonical_url:    props.blog?.canonical_url    ?? '',
    og_image_url:     props.blog?.og_image_url     ?? '',
    head_script:      props.blog?.head_script      ?? '',
    body_script:      props.blog?.body_script      ?? '',
    cover_image:      null,
    thumbnail:        null,
});

// Auto-generate slug preview
const slugPreview = computed(() =>
    (props.blog?.slug ?? form.title.toLowerCase().replace(/[^a-z0-9ก-๙]+/g, '-').replace(/^-+|-+$/g, ''))
);

// ─── Tags (chip input) ─────────────────────────────────────────────────────────
const tagInput = ref('');
function addTag() {
    const t = tagInput.value.trim();
    if (t && !form.tags.includes(t)) form.tags.push(t);
    tagInput.value = '';
}
function removeTag(t) { form.tags = form.tags.filter(x => x !== t); }
function onTagKeydown(e) {
    if (e.key === 'Enter' || e.key === ',') { e.preventDefault(); addTag(); }
    if (e.key === 'Backspace' && !tagInput.value) form.tags.pop();
}

// ─── Cover image ───────────────────────────────────────────────────────────────
const coverInput     = ref(null);
const thumbInput     = ref(null);
const coverPreview   = ref(props.blog?.cover_image_url ?? null);
const thumbPreview   = ref(props.blog?.thumbnail_url   ?? null);

function onCoverChange(e) {
    const f = e.target.files?.[0];
    if (!f) return;
    form.cover_image = f;
    coverPreview.value = URL.createObjectURL(f);
}
function onThumbChange(e) {
    const f = e.target.files?.[0];
    if (!f) return;
    form.thumbnail = f;
    thumbPreview.value = URL.createObjectURL(f);
}

// ─── SEO counters ──────────────────────────────────────────────────────────────
const metaTitleLen = computed(() => form.meta_title.length);
const metaDescLen  = computed(() => form.meta_description.length);

// ─── Accordion ─────────────────────────────────────────────────────────────────
const openSeo      = ref(false);
const openAdvanced = ref(false);

// ─── Submit ───────────────────────────────────────────────────────────────────
function submit() {
    if (isEdit.value) {
        form.post(route('admin.blog.update', props.blog.id));
    } else {
        form.post(route('admin.blog.store'));
    }
}
</script>

<template>
    <Head :title="isEdit ? 'แก้ไขบทความ' : 'เขียนบทความใหม่'" />
    <AdminLayout>

        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <Link :href="route('admin.blog')" class="text-gray-400 hover:text-gray-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
            </Link>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ isEdit ? 'แก้ไขบทความ' : 'เขียนบทความใหม่' }}
            </h1>
        </div>

        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                <!-- ── LEFT: editor (2/3) ──────────────────────────────────── -->
                <div class="xl:col-span-2 space-y-5">

                    <!-- Title + Slug -->
                    <div class="bg-white rounded-xl shadow-sm p-5 space-y-3">
                        <div>
                            <input v-model="form.title" type="text" placeholder="ชื่อบทความ..."
                                   class="w-full text-2xl font-bold border-0 border-b border-gray-200 pb-2 focus:outline-none focus:border-blue-500 transition bg-transparent"/>
                            <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</p>
                        </div>
                        <p class="text-xs text-gray-400">
                            URL: <span class="font-mono text-gray-600">/blog/{{ slugPreview || 'slug-บทความ' }}</span>
                        </p>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">สรุปบทความ (Excerpt) *</label>
                            <textarea v-model="form.excerpt" rows="2" maxlength="500"
                                      placeholder="สรุปสั้น ๆ ใช้แสดงใน card และ meta description..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"/>
                            <p class="text-right text-xs text-gray-400 mt-0.5">{{ form.excerpt.length }}/500</p>
                        </div>
                    </div>

                    <!-- Cover Image -->
                    <div class="bg-white rounded-xl shadow-sm p-5 space-y-3">
                        <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">ภาพปก (Cover Image)</h2>

                        <div v-if="coverPreview" class="relative rounded-xl overflow-hidden aspect-video">
                            <img :src="coverPreview" class="w-full h-full object-cover"/>
                            <button type="button" @click="coverPreview = null; form.cover_image = null"
                                    class="absolute top-2 right-2 bg-black/50 hover:bg-black/70 text-white rounded-full p-1.5 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div @click="coverInput.click()"
                             class="flex items-center justify-center border-2 border-dashed border-gray-300 rounded-xl py-8 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                            <div class="text-center">
                                <svg class="mx-auto w-7 h-7 text-gray-400 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/></svg>
                                <p class="text-sm text-gray-500">{{ coverPreview ? 'เปลี่ยนภาพปก' : 'คลิกเพื่อเลือกภาพปก' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">JPG / PNG / WebP ไม่เกิน 5 MB</p>
                            </div>
                        </div>
                        <input ref="coverInput" type="file" accept="image/*" class="hidden" @change="onCoverChange"/>
                        <p v-if="form.errors.cover_image" class="text-red-500 text-xs">{{ form.errors.cover_image }}</p>
                    </div>

                    <!-- Content Editor -->
                    <div class="bg-white rounded-xl shadow-sm p-5 space-y-3">
                        <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">เนื้อหาบทความ</h2>
                        <TipTapEditor
                            v-model="form.content"
                            :blog-uuid="draftUuid"
                            :upload-url="route('admin.blog.media.upload')"
                            placeholder="เริ่มเขียนบทความที่นี่..."
                        />
                        <p v-if="form.errors.content" class="text-red-500 text-xs">{{ form.errors.content }}</p>
                    </div>

                    <!-- SEO Accordion -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <button type="button"
                                @click="openSeo = !openSeo"
                                class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-gray-50 transition">
                            <span class="font-semibold text-gray-800">SEO & Meta</span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="openSeo ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                            </svg>
                        </button>
                        <div v-show="openSeo" class="px-5 pb-5 space-y-4 border-t border-gray-100">
                            <div class="pt-4">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-xs font-medium text-gray-500">Meta Title</label>
                                    <span class="text-xs" :class="metaTitleLen > 60 ? 'text-amber-500' : 'text-gray-400'">{{ metaTitleLen }}/70</span>
                                </div>
                                <input v-model="form.meta_title" type="text" maxlength="70"
                                       :placeholder="form.title || 'ปล่อยว่างเพื่อใช้ชื่อบทความ'"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-xs font-medium text-gray-500">Meta Description</label>
                                    <span class="text-xs" :class="metaDescLen > 145 ? 'text-amber-500' : 'text-gray-400'">{{ metaDescLen }}/160</span>
                                </div>
                                <textarea v-model="form.meta_description" rows="2" maxlength="160"
                                          :placeholder="form.excerpt || 'ปล่อยว่างเพื่อใช้ excerpt'"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"/>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Keywords (คั่นด้วยจุลภาค)</label>
                                <input v-model="form.meta_keywords" type="text" placeholder="เว็บไซต์, SEO, ออกแบบ"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">OG Image URL (social share)</label>
                                <input v-model="form.og_image_url" type="text" placeholder="https://..."
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Canonical URL</label>
                                <input v-model="form.canonical_url" type="text" placeholder="https://..."
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Scripts Accordion -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <button type="button"
                                @click="openAdvanced = !openAdvanced"
                                class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-gray-50 transition">
                            <span class="font-semibold text-gray-800">Scripts (โฆษณา / Tracking)</span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="openAdvanced ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                            </svg>
                        </button>
                        <div v-show="openAdvanced" class="px-5 pb-5 space-y-4 border-t border-gray-100">
                            <p class="text-xs text-amber-600 bg-amber-50 rounded-lg px-3 py-2 mt-4">
                                Script เหล่านี้จะถูก inject เฉพาะหน้าบทความนี้เท่านั้น เช่น Facebook Pixel, Google Ads tracking
                            </p>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Head Script (ใน &lt;head&gt;)</label>
                                <textarea v-model="form.head_script" rows="4" spellcheck="false"
                                          placeholder="&lt;script&gt;...&lt;/script&gt;"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none bg-gray-50"/>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Body Script (ก่อน &lt;/body&gt;)</label>
                                <textarea v-model="form.body_script" rows="4" spellcheck="false"
                                          placeholder="&lt;script&gt;...&lt;/script&gt;"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none bg-gray-50"/>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── RIGHT: sidebar (1/3) ─────────────────────────────── -->
                <div class="space-y-5">

                    <!-- Publish -->
                    <div class="bg-white rounded-xl shadow-sm p-5 space-y-4">
                        <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">การเผยแพร่</h2>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">สถานะ</label>
                            <select v-model="form.status"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="draft">Draft (แบบร่าง)</option>
                                <option value="published">เผยแพร่ทันที</option>
                                <option value="scheduled">ตั้งเวลาเผยแพร่</option>
                                <option value="archived">Archive</option>
                            </select>
                        </div>

                        <div v-if="form.status === 'scheduled'">
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">วันและเวลาที่จะเผยแพร่</label>
                            <input v-model="form.published_at" type="datetime-local"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            <p v-if="form.errors.published_at" class="text-red-500 text-xs mt-1">{{ form.errors.published_at }}</p>
                        </div>

                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" v-model="form.is_featured" class="rounded text-blue-600"/>
                            <span class="text-sm text-gray-700">ปักหมุด (Featured)</span>
                        </label>

                        <!-- Progress -->
                        <div v-if="form.progress" class="space-y-1">
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>กำลังอัปโหลด...</span>
                                <span>{{ form.progress.percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all" :style="{ width: form.progress.percentage + '%' }"></div>
                            </div>
                        </div>

                        <button type="submit" :disabled="form.processing"
                                class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white font-semibold py-2.5 rounded-lg transition text-sm">
                            {{ form.processing ? 'กำลังบันทึก...' : (isEdit ? 'บันทึกการแก้ไข' : 'บันทึกบทความ') }}
                        </button>
                        <Link :href="route('admin.blog')"
                              class="block text-center text-sm text-gray-500 hover:text-gray-700">ยกเลิก</Link>
                    </div>

                    <!-- Category -->
                    <div class="bg-white rounded-xl shadow-sm p-5 space-y-3">
                        <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">หมวดหมู่</h2>
                        <select v-model="form.category_id"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">— ไม่ระบุ —</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Tags -->
                    <div class="bg-white rounded-xl shadow-sm p-5 space-y-3">
                        <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">Tags</h2>
                        <div class="flex flex-wrap gap-1.5 min-h-[32px] p-2 border border-gray-300 rounded-lg focus-within:ring-2 focus-within:ring-blue-500">
                            <span v-for="tag in form.tags" :key="tag"
                                  class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 text-xs font-medium px-2 py-0.5 rounded-full">
                                {{ tag }}
                                <button type="button" @click="removeTag(tag)" class="hover:text-red-500">×</button>
                            </span>
                            <input v-model="tagInput" type="text" placeholder="พิมพ์แล้วกด Enter"
                                   class="flex-1 min-w-[80px] text-xs outline-none bg-transparent"
                                   @keydown="onTagKeydown" @blur="addTag"/>
                        </div>
                        <p class="text-xs text-gray-400">กด Enter หรือ , เพื่อเพิ่ม tag</p>
                    </div>

                    <!-- Thumbnail -->
                    <div class="bg-white rounded-xl shadow-sm p-5 space-y-3">
                        <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">Thumbnail (listing card)</h2>
                        <p class="text-xs text-gray-400">ถ้าไม่ใส่ จะใช้ภาพปกแทน</p>
                        <div v-if="thumbPreview" class="relative rounded-lg overflow-hidden aspect-video">
                            <img :src="thumbPreview" class="w-full h-full object-cover"/>
                            <button type="button" @click="thumbPreview = null; form.thumbnail = null"
                                    class="absolute top-1 right-1 bg-black/50 hover:bg-black/70 text-white rounded-full p-1 transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div @click="thumbInput.click()"
                             class="flex items-center justify-center border-2 border-dashed border-gray-300 rounded-xl py-4 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                            <p class="text-xs text-gray-500">{{ thumbPreview ? 'เปลี่ยนรูป' : 'คลิกเพื่อเลือก thumbnail' }}</p>
                        </div>
                        <input ref="thumbInput" type="file" accept="image/*" class="hidden" @change="onThumbChange"/>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
