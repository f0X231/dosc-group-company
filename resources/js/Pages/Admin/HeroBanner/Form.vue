<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    banner: { type: Object, default: null },
});

const isEdit = computed(() => !!props.banner);

// ─── Form ─────────────────────────────────────────────────────────────────────
const form = useForm({
    badge_text:         props.banner?.badge_text         ?? '',
    headline:           props.banner?.headline           ?? '',
    headline_highlight: props.banner?.headline_highlight ?? '',
    subtext:            props.banner?.subtext            ?? '',
    cta_text:           props.banner?.cta_text           ?? 'ปรึกษาฟรี',
    cta_url:            props.banner?.cta_url            ?? '',
    stats:              props.banner?.stats              ?? [],
    media_type:         props.banner?.media_type         ?? 'none',
    overlay_opacity:    props.banner?.overlay_opacity    ?? 40,
    overlay_color:      props.banner?.overlay_color      ?? '#000000',
    image_position:     props.banner?.image_position     ?? 'right',
    image_size:         props.banner?.image_size         ?? 'md',
    image_align_y:      props.banner?.image_align_y      ?? 'center',
    bg_type:            props.banner?.bg_type            ?? 'gradient',
    bg_color_from:      props.banner?.bg_color_from      ?? '#f1f1f1',
    bg_color_to:        props.banner?.bg_color_to        ?? '#e5e5e5',
    text_color:         props.banner?.text_color         ?? 'dark',
    animation_style:    props.banner?.animation_style    ?? 'slide_up',
    status:             props.banner?.status             ?? 'inactive',
    media_file:         null,
});

// ─── Media upload ──────────────────────────────────────────────────────────────
const mediaInput   = ref(null);
const previewUrl   = ref(props.banner?.media_url ?? null);
const mediaError   = ref('');
const isVideoFile  = ref(props.banner?.media_type === 'video');

const LIMITS = { video: 30 * 1024 * 1024, image: 5 * 1024 * 1024 };

function onMediaChange(e) {
    const file = e.target.files?.[0];
    mediaError.value = '';
    if (!file) return;

    const isVideo = file.type.startsWith('video/');
    const limit   = isVideo ? LIMITS.video : LIMITS.image;

    if (file.size > limit) {
        mediaError.value = isVideo ? 'วิดีโอไม่เกิน 30 MB' : 'รูปภาพไม่เกิน 5 MB';
        e.target.value = '';
        return;
    }

    form.media_file = file;
    isVideoFile.value = isVideo;
    previewUrl.value  = URL.createObjectURL(file);
}

function clearMedia() {
    form.media_file   = null;
    previewUrl.value  = props.banner?.media_url ?? null;
    isVideoFile.value = props.banner?.media_type === 'video';
    mediaError.value  = '';
}

// Accept attribute by media_type
const mediaAccept = computed(() => {
    if (form.media_type === 'video') return 'video/mp4,video/quicktime';
    return 'image/jpeg,image/png,image/webp,image/gif';
});

// ─── Stats ─────────────────────────────────────────────────────────────────────
function addStat()    { form.stats.push({ label: '', value: '', suffix: '+' }); }
function removeStat(i) { form.stats.splice(i, 1); }

// ─── Live preview ──────────────────────────────────────────────────────────────
const previewBg = computed(() => {
    if (form.bg_type === 'solid') return `background-color: ${form.bg_color_from}`;
    return `background: linear-gradient(135deg, ${form.bg_color_from}, ${form.bg_color_to})`;
});

const previewOverlay = computed(() => {
    const hex = form.overlay_color ?? '#000000';
    const r   = parseInt(hex.slice(1, 3), 16);
    const g   = parseInt(hex.slice(3, 5), 16);
    const b   = parseInt(hex.slice(5, 7), 16);
    return `rgba(${r},${g},${b},${form.overlay_opacity / 100})`;
});

const textDark = computed(() => form.text_color === 'dark');

// ─── Submit ───────────────────────────────────────────────────────────────────
function submit() {
    if (isEdit.value) {
        form.post(route('admin.hero-banner.update', props.banner.id));
    } else {
        form.post(route('admin.hero-banner.store'));
    }
}
</script>

<template>
    <Head :title="isEdit ? 'แก้ไข Hero Banner' : 'สร้าง Hero Banner'" />
    <AdminLayout>

        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <Link :href="route('admin.hero-banner')" class="text-gray-400 hover:text-gray-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
            </Link>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ isEdit ? 'แก้ไข Hero Banner' : 'สร้าง Hero Banner ใหม่' }}
            </h1>
        </div>

        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

                <!-- ── LEFT: form (3/5) ──────────────────────────────────── -->
                <div class="xl:col-span-3 space-y-5">

                    <!-- Content -->
                    <div class="bg-white rounded-xl shadow-sm p-5 space-y-4">
                        <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">เนื้อหา</h2>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Badge (ข้อความเล็กเหนือ Headline)</label>
                            <input v-model="form.badge_text" type="text" placeholder="เช่น พาร์ทเนอร์ธุรกิจของคุณ"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Headline *</label>
                                <input v-model="form.headline" type="text" placeholder="รับทำเว็บไซต์"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                                <p v-if="form.errors.headline" class="text-red-500 text-xs mt-1">{{ form.errors.headline }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Headline Highlight (สีแตกต่าง)</label>
                                <input v-model="form.headline_highlight" type="text" placeholder="DOSC DIGITAL"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Subtext</label>
                            <textarea v-model="form.subtext" rows="2" placeholder="คำอธิบายสั้น ๆ..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">ข้อความ CTA Button</label>
                                <input v-model="form.cta_text" type="text"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">URL CTA Button</label>
                                <input v-model="form.cta_url" type="text" placeholder="https://line.me/..."
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            </div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="bg-white rounded-xl shadow-sm p-5 space-y-3">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h2 class="font-semibold text-gray-800">Stats (ตัวเลขสถิติ)</h2>
                            <button type="button" @click="addStat"
                                    class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                + เพิ่ม
                            </button>
                        </div>
                        <p class="text-xs text-gray-400">เช่น 150+ ผลงาน, 80+ ลูกค้า — แสดงที่ด้านล่าง hero</p>

                        <div v-if="form.stats.length === 0" class="text-xs text-gray-400 py-2">
                            ยังไม่มี stat — กด + เพิ่ม
                        </div>

                        <div v-for="(stat, i) in form.stats" :key="i"
                             class="grid grid-cols-7 gap-2 items-center">
                            <input v-model="stat.value" type="text" placeholder="150"
                                   class="col-span-2 border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            <input v-model="stat.suffix" type="text" placeholder="+"
                                   class="col-span-1 border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            <input v-model="stat.label" type="text" placeholder="ผลงาน"
                                   class="col-span-3 border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                            <button type="button" @click="removeStat(i)"
                                    class="col-span-1 text-gray-400 hover:text-red-500 flex justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Media -->
                    <div class="bg-white rounded-xl shadow-sm p-5 space-y-4">
                        <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">Media</h2>

                        <!-- Type selector -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">ประเภท Media</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <button v-for="opt in [
                                    { value: 'none',       label: 'ไม่มี', icon: '🚫' },
                                    { value: 'video',      label: 'วิดีโอ BG', icon: '🎬' },
                                    { value: 'image_full', label: 'รูปเต็ม BG', icon: '🖼️' },
                                    { value: 'image_side', label: 'Illustration', icon: '🎨' },
                                ]" :key="opt.value"
                                    type="button"
                                    @click="form.media_type = opt.value"
                                    class="py-2 px-3 rounded-lg border text-xs font-medium text-center transition"
                                    :class="form.media_type === opt.value
                                        ? 'border-blue-500 bg-blue-50 text-blue-700'
                                        : 'border-gray-200 text-gray-600 hover:border-gray-300'">
                                    <div class="text-lg mb-0.5">{{ opt.icon }}</div>
                                    {{ opt.label }}
                                </button>
                            </div>
                        </div>

                        <!-- File upload (not none) -->
                        <div v-if="form.media_type !== 'none'" class="space-y-3">
                            <!-- Preview -->
                            <div v-if="previewUrl" class="relative rounded-xl overflow-hidden bg-gray-900 h-32">
                                <video v-if="isVideoFile" :src="previewUrl"
                                       class="w-full h-full object-cover opacity-70" muted autoplay loop playsinline/>
                                <img  v-else :src="previewUrl"
                                      class="w-full h-full object-cover opacity-80"
                                      :class="form.media_type === 'image_side' ? 'object-contain' : 'object-cover'"/>
                                <button type="button" @click="clearMedia"
                                        class="absolute top-2 right-2 bg-black/50 text-white rounded-full p-1 hover:bg-black/70 transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Upload area -->
                            <div @click="mediaInput.click()"
                                 class="flex items-center justify-center border-2 border-dashed border-gray-300 rounded-xl px-4 py-5 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                                <div class="text-center">
                                    <svg class="mx-auto w-6 h-6 text-gray-400 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                                    </svg>
                                    <p class="text-xs text-gray-500">
                                        {{ previewUrl ? 'เปลี่ยนไฟล์' : 'คลิกเพื่อเลือกไฟล์' }}
                                    </p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">
                                        {{ form.media_type === 'video' ? 'MP4 ไม่เกิน 30 MB' : 'JPG / PNG / WebP / GIF ไม่เกิน 5 MB' }}
                                    </p>
                                </div>
                            </div>
                            <input ref="mediaInput" type="file" :accept="mediaAccept" class="hidden" @change="onMediaChange"/>
                            <p v-if="mediaError" class="text-red-500 text-xs">{{ mediaError }}</p>
                            <p v-if="form.errors.media_file" class="text-red-500 text-xs">{{ form.errors.media_file }}</p>

                            <!-- Overlay (video / image_full) -->
                            <div v-if="form.media_type === 'video' || form.media_type === 'image_full'"
                                 class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">
                                        Overlay Opacity: {{ form.overlay_opacity }}%
                                    </label>
                                    <input v-model.number="form.overlay_opacity" type="range" min="0" max="90" step="5"
                                           class="w-full accent-blue-600"/>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Overlay Color</label>
                                    <div class="flex gap-2 items-center">
                                        <input v-model="form.overlay_color" type="color"
                                               class="w-8 h-8 rounded border border-gray-300 cursor-pointer p-0.5"/>
                                        <input v-model="form.overlay_color" type="text"
                                               class="flex-1 border border-gray-300 rounded-lg px-2 py-1.5 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                                    </div>
                                </div>
                            </div>

                            <!-- Image side options -->
                            <div v-if="form.media_type === 'image_side'" class="space-y-3 pt-1">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-2">ตำแหน่ง</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button v-for="pos in [{ v: 'left', label: '← รูปซ้าย / ข้อความขวา' }, { v: 'right', label: 'ข้อความซ้าย / รูปขวา →' }]"
                                                :key="pos.v"
                                                type="button"
                                                @click="form.image_position = pos.v"
                                                class="py-2 px-3 rounded-lg border text-xs font-medium transition"
                                                :class="form.image_position === pos.v
                                                    ? 'border-blue-500 bg-blue-50 text-blue-700'
                                                    : 'border-gray-200 text-gray-600 hover:border-gray-300'">
                                            {{ pos.label }}
                                        </button>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">ขนาดรูป</label>
                                        <div class="flex gap-1">
                                            <button v-for="sz in [{ v: 'sm', l: 'เล็ก' }, { v: 'md', l: 'กลาง' }, { v: 'lg', l: 'ใหญ่' }]"
                                                    :key="sz.v"
                                                    type="button"
                                                    @click="form.image_size = sz.v"
                                                    class="flex-1 py-1.5 rounded-lg border text-xs font-medium transition"
                                                    :class="form.image_size === sz.v
                                                        ? 'border-blue-500 bg-blue-50 text-blue-700'
                                                        : 'border-gray-200 text-gray-600 hover:border-gray-300'">
                                                {{ sz.l }}
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-2">แนวตั้ง</label>
                                        <div class="flex gap-1">
                                            <button v-for="al in [{ v: 'top', l: 'บน' }, { v: 'center', l: 'กลาง' }, { v: 'bottom', l: 'ล่าง' }]"
                                                    :key="al.v"
                                                    type="button"
                                                    @click="form.image_align_y = al.v"
                                                    class="flex-1 py-1.5 rounded-lg border text-xs font-medium transition"
                                                    :class="form.image_align_y === al.v
                                                        ? 'border-blue-500 bg-blue-50 text-blue-700'
                                                        : 'border-gray-200 text-gray-600 hover:border-gray-300'">
                                                {{ al.l }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Background & Style -->
                    <div class="bg-white rounded-xl shadow-sm p-5 space-y-4">
                        <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">Background & Style</h2>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-2">Background</label>
                            <div class="grid grid-cols-2 gap-2 mb-3">
                                <button v-for="bt in [{ v: 'gradient', l: 'Gradient' }, { v: 'solid', l: 'Solid Color' }]"
                                        :key="bt.v"
                                        type="button"
                                        @click="form.bg_type = bt.v"
                                        class="py-1.5 rounded-lg border text-xs font-medium transition"
                                        :class="form.bg_type === bt.v
                                            ? 'border-blue-500 bg-blue-50 text-blue-700'
                                            : 'border-gray-200 text-gray-600 hover:border-gray-300'">
                                    {{ bt.l }}
                                </button>
                            </div>
                            <div class="flex gap-3">
                                <div class="flex-1">
                                    <label class="block text-[10px] text-gray-400 mb-1">{{ form.bg_type === 'gradient' ? 'จาก' : 'สี' }}</label>
                                    <div class="flex gap-2 items-center">
                                        <input v-model="form.bg_color_from" type="color"
                                               class="w-8 h-8 rounded border border-gray-300 cursor-pointer p-0.5"/>
                                        <input v-model="form.bg_color_from" type="text"
                                               class="flex-1 border border-gray-300 rounded-lg px-2 py-1.5 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                                    </div>
                                </div>
                                <div v-if="form.bg_type === 'gradient'" class="flex-1">
                                    <label class="block text-[10px] text-gray-400 mb-1">ถึง</label>
                                    <div class="flex gap-2 items-center">
                                        <input v-model="form.bg_color_to" type="color"
                                               class="w-8 h-8 rounded border border-gray-300 cursor-pointer p-0.5"/>
                                        <input v-model="form.bg_color_to" type="text"
                                               class="flex-1 border border-gray-300 rounded-lg px-2 py-1.5 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-2">สีข้อความ</label>
                                <div class="flex gap-2">
                                    <button v-for="tc in [{ v: 'dark', l: '🌑 Dark' }, { v: 'light', l: '⬜ Light' }]"
                                            :key="tc.v"
                                            type="button"
                                            @click="form.text_color = tc.v"
                                            class="flex-1 py-1.5 rounded-lg border text-xs font-medium transition"
                                            :class="form.text_color === tc.v
                                                ? 'border-blue-500 bg-blue-50 text-blue-700'
                                                : 'border-gray-200 text-gray-600 hover:border-gray-300'">
                                        {{ tc.l }}
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-2">Animation</label>
                                <select v-model="form.animation_style"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="slide_up">Slide Up</option>
                                    <option value="typewriter">Typewriter</option>
                                    <option value="fade">Fade</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── RIGHT: preview + publish (2/5) ────────────────────── -->
                <div class="xl:col-span-2 space-y-5">

                    <!-- Live Preview -->
                    <div class="bg-white rounded-xl shadow-sm p-4">
                        <h2 class="font-semibold text-gray-800 mb-3 text-sm">Preview</h2>

                        <!-- Mini hero -->
                        <div class="relative rounded-xl overflow-hidden h-48" :style="previewBg">

                            <!-- Full bg media -->
                            <template v-if="previewUrl && (form.media_type === 'video' || form.media_type === 'image_full')">
                                <video v-if="form.media_type === 'video'" :src="previewUrl"
                                       class="absolute inset-0 w-full h-full object-cover" muted autoplay loop playsinline/>
                                <img v-else :src="previewUrl"
                                     class="absolute inset-0 w-full h-full object-cover"/>
                                <div class="absolute inset-0" :style="`background: ${previewOverlay}`"></div>
                            </template>

                            <!-- image_side layout -->
                            <div v-if="form.media_type === 'image_side' && previewUrl"
                                 class="absolute inset-0 flex"
                                 :class="form.image_position === 'left' ? 'flex-row-reverse' : 'flex-row'">
                                <div class="flex-1"></div>
                                <div class="flex"
                                     :class="{
                                         'w-1/3': form.image_size === 'sm',
                                         'w-2/5': form.image_size === 'md',
                                         'w-1/2': form.image_size === 'lg',
                                         'items-start': form.image_align_y === 'top',
                                         'items-center': form.image_align_y === 'center',
                                         'items-end': form.image_align_y === 'bottom',
                                     }">
                                    <img :src="previewUrl" class="w-full h-full object-contain"/>
                                </div>
                            </div>

                            <!-- Text overlay on preview -->
                            <div class="absolute inset-0 flex flex-col justify-center px-5"
                                 :class="form.media_type === 'image_side' && form.image_position === 'right' ? 'items-start' : (form.media_type === 'image_side' && form.image_position === 'left' ? 'items-end' : 'items-start')">
                                <p v-if="form.badge_text"
                                   class="text-[8px] font-bold tracking-widest uppercase mb-1 opacity-70"
                                   :class="textDark ? 'text-gray-600' : 'text-white'">
                                    {{ form.badge_text }}
                                </p>
                                <p class="text-sm font-bold leading-tight"
                                   :class="textDark ? 'text-gray-900' : 'text-white'">
                                    {{ form.headline || 'Headline' }}
                                    <span v-if="form.headline_highlight" class="text-red-500"> {{ form.headline_highlight }}</span>
                                </p>
                                <p v-if="form.subtext" class="text-[9px] mt-1 opacity-70 line-clamp-2 max-w-[60%]"
                                   :class="textDark ? 'text-gray-700' : 'text-white'">
                                    {{ form.subtext }}
                                </p>
                                <div v-if="form.cta_text"
                                     class="mt-2 px-3 py-1 rounded-full text-[9px] font-bold border-2 inline-block"
                                     :class="textDark ? 'border-gray-900 text-gray-900' : 'border-white text-white'">
                                    {{ form.cta_text }}
                                </div>
                            </div>

                            <!-- Stats preview -->
                            <div v-if="form.stats?.length"
                                 class="absolute bottom-0 left-0 right-0 flex gap-3 px-5 py-2 border-t"
                                 :class="textDark ? 'border-black/10' : 'border-white/20'">
                                <div v-for="(stat, i) in form.stats.slice(0, 3)" :key="i" class="text-center">
                                    <p class="text-[10px] font-bold"
                                       :class="textDark ? 'text-gray-900' : 'text-white'">
                                        {{ stat.value }}{{ stat.suffix }}
                                    </p>
                                    <p class="text-[8px] opacity-60"
                                       :class="textDark ? 'text-gray-700' : 'text-white'">
                                        {{ stat.label }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Publish -->
                    <div class="bg-white rounded-xl shadow-sm p-5 space-y-4">
                        <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">การเผยแพร่</h2>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1.5">สถานะ</label>
                            <select v-model="form.status"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="inactive">Draft (ไม่แสดง)</option>
                                <option value="active">Active (แสดง)</option>
                            </select>
                        </div>

                        <!-- Upload progress -->
                        <div v-if="form.progress" class="space-y-1">
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>กำลังอัปโหลด...</span>
                                <span>{{ form.progress.percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all"
                                     :style="{ width: form.progress.percentage + '%' }"></div>
                            </div>
                        </div>

                        <button type="submit" :disabled="form.processing"
                                class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white font-semibold py-2.5 rounded-lg transition text-sm">
                            {{ form.processing ? 'กำลังบันทึก...' : (isEdit ? 'บันทึกการแก้ไข' : 'สร้าง Banner') }}
                        </button>
                        <Link :href="route('admin.hero-banner')"
                              class="block text-center text-sm text-gray-500 hover:text-gray-700">
                            ยกเลิก
                        </Link>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
