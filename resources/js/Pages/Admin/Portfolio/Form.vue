<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    portfolio: { type: Object, default: null },
    packages:  { type: Array, default: () => [] },
});

const isEdit = computed(() => !!props.portfolio);

const form = useForm({
    title:       props.portfolio?.title       ?? '',
    package_id:  props.portfolio?.package_id  ?? '',
    client_url:  props.portfolio?.client_url  ?? '',
    description: props.portfolio?.description ?? '',
    status:      props.portfolio?.status      ?? 'active',
    video:       null,
    thumbnail:   null,
});

// ─── Video preview ────────────────────────────────────────────────────────────
const videoPreviewUrl  = ref(props.portfolio?.video_url     ?? null);
const thumbPreviewUrl  = ref(props.portfolio?.thumbnail_url ?? null);
const videoError       = ref('');
const videoInput       = ref(null);
const thumbInput       = ref(null);

function onVideoChange(e) {
    const file = e.target.files?.[0];
    videoError.value = '';
    if (!file) return;

    if (file.size > 6 * 1024 * 1024) {
        videoError.value = 'ไฟล์วิดีโอต้องไม่เกิน 6 MB';
        e.target.value = '';
        return;
    }

    form.video = file;
    videoPreviewUrl.value = URL.createObjectURL(file);
}

function onThumbnailChange(e) {
    const file = e.target.files?.[0];
    if (!file) return;
    form.thumbnail = file;
    thumbPreviewUrl.value = URL.createObjectURL(file);
}

function clearVideo() {
    form.video = null;
    videoPreviewUrl.value = props.portfolio?.video_url ?? null;
    videoError.value = '';
}

function clearThumbnail() {
    form.thumbnail = null;
    thumbPreviewUrl.value = props.portfolio?.thumbnail_url ?? null;
}

// ─── Submit ───────────────────────────────────────────────────────────────────
function submit() {
    if (isEdit.value) {
        form.post(route('admin.portfolio.update', props.portfolio.id));
    } else {
        form.post(route('admin.portfolio.store'));
    }
}

function formatSize(bytes) {
    if (!bytes) return '';
    return ' (' + (bytes / 1024 / 1024).toFixed(1) + ' MB)';
}
</script>

<template>
    <Head :title="isEdit ? 'แก้ไขผลงาน' : 'เพิ่มผลงาน'" />
    <AdminLayout>
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <Link :href="route('admin.portfolio')" class="text-gray-400 hover:text-gray-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
            </Link>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ isEdit ? 'แก้ไขผลงาน' : 'เพิ่มผลงานใหม่' }}
            </h1>
        </div>

        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left: main info -->
                <div class="lg:col-span-2 space-y-5">

                    <!-- Title -->
                    <div class="bg-white rounded-xl shadow-sm p-5 space-y-4">
                        <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">ข้อมูลผลงาน</h2>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">ชื่อผลงาน / ชื่อลูกค้า *</label>
                            <input v-model="form.title" type="text" placeholder="เช่น ร้านอาหารครัวคุณแม่"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">แพ็กเกจ</label>
                                <select v-model="form.package_id"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">— ไม่ระบุ —</option>
                                    <option v-for="pkg in packages" :key="pkg.id" :value="pkg.id">
                                        {{ pkg.tag_text || pkg.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.package_id" class="text-red-500 text-xs mt-1">{{ form.errors.package_id }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">ลิงก์เว็บไซต์ลูกค้า</label>
                                <input v-model="form.client_url" type="url" placeholder="https://example.com"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                <p v-if="form.errors.client_url" class="text-red-500 text-xs mt-1">{{ form.errors.client_url }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">คำอธิบาย (ไม่บังคับ)</label>
                            <textarea v-model="form.description" rows="3" placeholder="รายละเอียดเพิ่มเติม..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Video Upload -->
                    <div class="bg-white rounded-xl shadow-sm p-5 space-y-4">
                        <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">
                            วิดีโอ{{ isEdit ? ' (เว้นว่างถ้าไม่ต้องการเปลี่ยน)' : ' *' }}
                        </h2>

                        <!-- Preview -->
                        <div v-if="videoPreviewUrl" class="flex gap-4 items-start">
                            <div class="w-32 h-48 rounded-xl overflow-hidden bg-black flex-shrink-0">
                                <video :src="videoPreviewUrl" class="w-full h-full object-cover"
                                       muted loop autoplay playsinline />
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-600 mb-2">
                                    {{ isEdit && !form.video ? 'วิดีโอปัจจุบัน' : 'ตัวอย่างวิดีโอที่เลือก' }}
                                    <span v-if="portfolio?.file_size" class="text-gray-400">
                                        {{ formatSize(portfolio.file_size) }}
                                    </span>
                                </p>
                                <button type="button" @click="clearVideo"
                                        class="text-xs text-red-500 hover:text-red-700">
                                    {{ form.video ? '✕ ยกเลิกการเลือกไฟล์ใหม่' : '' }}
                                </button>
                            </div>
                        </div>

                        <!-- File input -->
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-2">
                                {{ videoPreviewUrl ? 'เลือกวิดีโอใหม่' : 'เลือกไฟล์วิดีโอ' }}
                            </p>
                            <div @click="videoInput.click()"
                                 class="flex items-center justify-center border-2 border-dashed border-gray-300 rounded-xl px-6 py-8 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                                <div class="text-center">
                                    <svg class="mx-auto w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                                    </svg>
                                    <p class="text-sm text-gray-500">คลิกเพื่อเลือกไฟล์</p>
                                    <p class="text-xs text-gray-400 mt-1">MP4 ขนาดไม่เกิน 6 MB</p>
                                </div>
                            </div>
                            <input ref="videoInput" type="file" accept="video/mp4,video/quicktime"
                                   class="hidden" @change="onVideoChange" />
                            <p v-if="videoError" class="text-red-500 text-xs mt-1">{{ videoError }}</p>
                            <p v-if="form.errors.video" class="text-red-500 text-xs mt-1">{{ form.errors.video }}</p>
                        </div>
                    </div>

                    <!-- Thumbnail Upload -->
                    <div class="bg-white rounded-xl shadow-sm p-5 space-y-4">
                        <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">
                            รูป Thumbnail (ไม่บังคับ)
                        </h2>
                        <p class="text-xs text-gray-500">ถ้าไม่อัปโหลด browser จะแสดง frame แรกของวิดีโอแทน</p>

                        <div v-if="thumbPreviewUrl" class="flex gap-4 items-start">
                            <img :src="thumbPreviewUrl" class="w-24 h-36 object-cover rounded-lg" />
                            <button type="button" @click="clearThumbnail"
                                    class="text-xs text-red-500 hover:text-red-700 mt-1">
                                {{ form.thumbnail ? '✕ ยกเลิกการเลือกไฟล์ใหม่' : '✕ ลบ thumbnail' }}
                            </button>
                        </div>

                        <div>
                            <div @click="thumbInput.click()"
                                 class="flex items-center justify-center border-2 border-dashed border-gray-300 rounded-xl px-6 py-6 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                                <div class="text-center">
                                    <svg class="mx-auto w-6 h-6 text-gray-400 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z"/>
                                    </svg>
                                    <p class="text-xs text-gray-500">JPG / PNG / WebP ไม่เกิน 2 MB</p>
                                </div>
                            </div>
                            <input ref="thumbInput" type="file" accept="image/jpeg,image/png,image/webp"
                                   class="hidden" @change="onThumbnailChange" />
                            <p v-if="form.errors.thumbnail" class="text-red-500 text-xs mt-1">{{ form.errors.thumbnail }}</p>
                        </div>
                    </div>
                </div>

                <!-- Right: sidebar -->
                <div class="space-y-5">
                    <!-- Status & submit -->
                    <div class="bg-white rounded-xl shadow-sm p-5 space-y-4">
                        <h2 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">การเผยแพร่</h2>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">สถานะ</label>
                            <select v-model="form.status"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="active">แสดง</option>
                                <option value="inactive">ซ่อน</option>
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

                        <button type="submit"
                                :disabled="form.processing"
                                class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white font-semibold py-2.5 rounded-lg transition text-sm">
                            {{ form.processing ? 'กำลังบันทึก...' : (isEdit ? 'บันทึกการแก้ไข' : 'เพิ่มผลงาน') }}
                        </button>

                        <Link :href="route('admin.portfolio')"
                              class="block text-center text-sm text-gray-500 hover:text-gray-700 transition">
                            ยกเลิก
                        </Link>
                    </div>

                    <!-- Tips -->
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-xs text-amber-700 space-y-1.5">
                        <p class="font-semibold">คำแนะนำวิดีโอ</p>
                        <p>• แนวตั้ง (9:16) ดูดีที่สุดบน mobile</p>
                        <p>• ความยาว 5–15 วินาที เล่นวนซ้ำ</p>
                        <p>• ขนาดไฟล์ไม่เกิน 6 MB</p>
                        <p>• ไฟล์ .mp4 (H.264)</p>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
