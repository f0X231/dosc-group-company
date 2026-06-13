<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    package: { type: Object, default: null },
});

const isEdit = computed(() => !!props.package);

const form = useForm({
    name:               props.package?.name ?? '',
    slug:               props.package?.slug ?? '',
    tag_text:           props.package?.tag_text ?? '',
    page_structure:     props.package?.page_structure ?? '',
    price:              props.package?.price ?? '',
    original_price:     props.package?.original_price ?? '',
    is_vat_excluded:    props.package?.is_vat_excluded ?? true,
    description:        props.package?.description ?? '',
    color_from:         props.package?.color_from ?? '#f97316',
    color_to:           props.package?.color_to ?? '#ef4444',
    badge_text:         props.package?.badge_text ?? '',
    badge_color:        props.package?.badge_color ?? '#ef4444',
    cta_primary_text:   props.package?.cta_primary_text ?? 'ดูรายละเอียด',
    cta_primary_url:    props.package?.cta_primary_url ?? '',
    cta_secondary_text: props.package?.cta_secondary_text ?? 'ดูผลงาน',
    cta_secondary_url:  props.package?.cta_secondary_url ?? '',
    portfolio_url:      props.package?.portfolio_url ?? '',
    detail_url:         props.package?.detail_url ?? '',
    meta_title:         props.package?.meta_title ?? '',
    meta_description:   props.package?.meta_description ?? '',
    status:             props.package?.status ?? 'active',
    sort_order:         props.package?.sort_order ?? 0,
    features:           props.package?.features?.map(f => ({ ...f })) ?? [],
});

// ── Features ─────────────────────────────────────────────────────────────────
function addFeature() {
    form.features.push({ id: null, title: '', description: '', type: 'included', is_highlighted: false });
}

function removeFeature(index) {
    form.features.splice(index, 1);
}

function moveFeature(index, dir) {
    const arr = form.features;
    const target = index + dir;
    if (target < 0 || target >= arr.length) return;
    [arr[index], arr[target]] = [arr[target], arr[index]];
}

const featureDragging = ref(null);
function onFeatureDragStart(i) { featureDragging.value = i; }
function onFeatureDrop(i) {
    if (featureDragging.value === null || featureDragging.value === i) return;
    const arr = form.features;
    arr.splice(i, 0, arr.splice(featureDragging.value, 1)[0]);
    featureDragging.value = null;
}

// ── Submit ────────────────────────────────────────────────────────────────────
function submit() {
    if (isEdit.value) {
        form.put(route('admin.packages.update', props.package.id));
    } else {
        form.post(route('admin.packages.store'));
    }
}

const typeLabel = { included: '✅ มี', excluded: '❌ ไม่มี', addon: '➕ เสริม' };
const typeStyle = {
    included: 'bg-green-50 text-green-700 border-green-200',
    excluded: 'bg-red-50 text-red-600 border-red-200',
    addon:    'bg-blue-50 text-blue-600 border-blue-200',
};
</script>

<template>
    <Head :title="isEdit ? `แก้ไข ${package?.name}` : 'เพิ่มแพ็กเกจใหม่'" />
    <AdminLayout>

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <Link :href="route('admin.packages')"
                      class="text-sm text-gray-500 hover:text-gray-900 flex items-center gap-1 mb-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    กลับรายการ
                </Link>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ isEdit ? `แก้ไข: ${package?.name}` : 'เพิ่มแพ็กเกจใหม่' }}
                </h1>
            </div>

            <!-- Preview Gradient -->
            <div class="w-32 h-16 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-md"
                 :style="`background: linear-gradient(135deg, ${form.color_from}, ${form.color_to})`">
                {{ form.name || 'Preview' }}
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="grid lg:grid-cols-3 gap-6">

                <!-- ── Main Info ──────────────────────────────────────────── -->
                <div class="lg:col-span-2 space-y-5">

                    <!-- Basic Info Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">ข้อมูลหลัก</h2>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">ชื่อแพ็กเกจ *</label>
                                <input v-model="form.name" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Slug</label>
                                <input v-model="form.slug" type="text" placeholder="one-page" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tag (แถบบนการ์ด)</label>
                            <input v-model="form.tag_text" type="text" placeholder="เหมาะสำหรับโปรโมทธุรกิจ..." class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">โครงสร้างหน้า</label>
                            <input v-model="form.page_structure" type="text" placeholder="หน้าหลัก 1 หน้า" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">รายละเอียด</label>
                            <textarea v-model="form.description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Pricing Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">ราคา</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">ราคา (฿) *</label>
                                <input v-model.number="form.price" type="number" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                <p v-if="form.errors.price" class="text-red-500 text-xs mt-1">{{ form.errors.price }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">ราคาเดิม (฿) — ขีดฆ่า</label>
                                <input v-model.number="form.original_price" type="number" min="0" placeholder="ว่าง = ไม่มีส่วนลด" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                        </div>
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" v-model="form.is_vat_excluded" class="rounded" />
                            แสดง "*ราคายังไม่รวมภาษีมูลค่าเพิ่ม VAT 7%"
                        </label>
                    </div>

                    <!-- Visual Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">สี / Badge / ปุ่ม</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">สีเริ่มต้น Gradient</label>
                                <div class="flex gap-2">
                                    <input type="color" v-model="form.color_from" class="h-10 w-14 border rounded-lg cursor-pointer p-1" />
                                    <input type="text" v-model="form.color_from" class="flex-1 border border-gray-300 rounded-lg px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">สีปลาย Gradient</label>
                                <div class="flex gap-2">
                                    <input type="color" v-model="form.color_to" class="h-10 w-14 border rounded-lg cursor-pointer p-1" />
                                    <input type="text" v-model="form.color_to" class="flex-1 border border-gray-300 rounded-lg px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Badge Text</label>
                                <input v-model="form.badge_text" type="text" placeholder="BEST SELLER / HOT / ใหม่" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Badge Color</label>
                                <div class="flex gap-2">
                                    <input type="color" v-model="form.badge_color" class="h-10 w-14 border rounded-lg cursor-pointer p-1" />
                                    <input type="text" v-model="form.badge_color" class="flex-1 border border-gray-300 rounded-lg px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">ปุ่มหลัก — Label</label>
                                <input v-model="form.cta_primary_text" type="text" placeholder="ดูรายละเอียด" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">ปุ่มหลัก — URL</label>
                                <input v-model="form.cta_primary_url" type="text" placeholder="/contact" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">ปุ่มรอง — Label</label>
                                <input v-model="form.cta_secondary_text" type="text" placeholder="ดูผลงาน" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">ปุ่มรอง — URL</label>
                                <input v-model="form.cta_secondary_url" type="text" placeholder="/portfolio?package=one-page" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                        </div>
                    </div>

                    <!-- Features Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Features ({{ form.features.length }})</h2>
                            <button type="button" @click="addFeature"
                                    class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-700 font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                เพิ่ม Feature
                            </button>
                        </div>

                        <div class="space-y-3">
                            <div v-if="form.features.length === 0" class="text-center py-8 text-gray-400 text-sm border-2 border-dashed border-gray-200 rounded-lg">
                                กดเพิ่ม Feature ด้านบน
                            </div>

                            <div
                                v-for="(feature, i) in form.features"
                                :key="i"
                                draggable="true"
                                @dragstart="onFeatureDragStart(i)"
                                @dragover.prevent
                                @drop="onFeatureDrop(i)"
                                class="border border-gray-200 rounded-lg p-3 bg-gray-50 space-y-2"
                                :class="featureDragging === i ? 'opacity-40' : ''"
                            >
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-300 cursor-grab flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M8 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM8 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM8 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM20 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM20 18a2 2 0 1 1-4 0 2 2 0 0 1 4 0ZM20 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/></svg>
                                    <input v-model="feature.title" type="text" placeholder="ชื่อ Feature *"
                                           class="flex-1 border border-gray-300 rounded-md px-2.5 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" />
                                    <select v-model="feature.type"
                                            class="border border-gray-300 rounded-md px-2 py-1.5 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            :class="typeStyle[feature.type]">
                                        <option v-for="(label, val) in typeLabel" :key="val" :value="val">{{ label }}</option>
                                    </select>
                                    <label class="flex items-center gap-1 text-xs text-gray-500 cursor-pointer whitespace-nowrap">
                                        <input type="checkbox" v-model="feature.is_highlighted" class="rounded" />
                                        Highlight
                                    </label>
                                    <button type="button" @click="removeFeature(i)"
                                            class="text-gray-300 hover:text-red-500 transition flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <input v-model="feature.description" type="text" placeholder="คำอธิบาย (ไม่บังคับ)"
                                       class="w-full border border-gray-200 rounded-md px-2.5 py-1.5 text-xs text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── Sidebar ─────────────────────────────────────────────── -->
                <div class="space-y-5">

                    <!-- Publish Card -->
                    <div class="bg-white rounded-xl shadow-sm p-5 space-y-4">
                        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">เผยแพร่</h2>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">สถานะ</label>
                            <select v-model="form.status" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="active">แสดง (Active)</option>
                                <option value="inactive">ซ่อน (Inactive)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">ลำดับ</label>
                            <input v-model.number="form.sort_order" type="number" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div class="flex gap-2 pt-2 border-t border-gray-100">
                            <Link :href="route('admin.packages')" class="flex-1 text-center px-3 py-2.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                ยกเลิก
                            </Link>
                            <button type="submit" :disabled="form.processing"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white text-sm font-semibold py-2.5 rounded-lg transition">
                                {{ form.processing ? 'กำลังบันทึก...' : 'บันทึก' }}
                            </button>
                        </div>
                    </div>

                    <!-- SEO Card -->
                    <div class="bg-white rounded-xl shadow-sm p-5 space-y-4">
                        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">SEO</h2>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Meta Title</label>
                            <input v-model="form.meta_title" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Meta Description</label>
                            <textarea v-model="form.meta_description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>
                    </div>

                </div>
            </div>
        </form>

    </AdminLayout>
</template>
