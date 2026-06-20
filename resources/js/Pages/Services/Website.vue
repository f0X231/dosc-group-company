<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    packages:   { type: Array, default: () => [] },
    addons:     { type: Array, default: () => [] },
    workSteps:  { type: Array, default: () => [] },
    whyUsItems: { type: Array, default: () => [] },
    portfolios: { type: Array, default: () => [] },
});

const page = usePage();
const site = computed(() => page.props.site ?? {});

function formatPrice(n) {
    return n?.toLocaleString('th-TH') ?? '—';
}

const PACKAGE_IMAGES = [
    'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=800&h=560&fit=crop',
    'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=800&h=560&fit=crop',
    'https://images.unsplash.com/photo-1553877522-43269d4ea984?w=800&h=560&fit=crop',
    'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=560&fit=crop',
];

const STEP_IMAGES = [
    'https://images.unsplash.com/photo-1556761175-4b46a572b786?w=600&h=400&fit=crop',
    'https://images.unsplash.com/photo-1581291518857-4e27b48ff24e?w=600&h=400&fit=crop',
    'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&h=400&fit=crop',
    'https://images.unsplash.com/photo-1504868584819-f8e8b4b6d7e3?w=600&h=400&fit=crop',
];

const EVERY_PKG_BENEFITS = [
    { icon: 'design',   label: 'ออกแบบโดยมืออาชีพ' },
    { icon: 'edit',     label: 'ไม่จำกัดแก้ไข (30 วัน)' },
    { icon: 'support',  label: 'ดูแลหลัง Sale' },
    { icon: 'bolt',     label: 'Fast Loading' },
    { icon: 'globe',    label: 'โดเมน + โฮสติ้ง ฟรี 1 ปี' },
    { icon: 'academic', label: 'อบรมการใช้งาน' },
    { icon: 'mobile',   label: 'รองรับ Mobile' },
    { icon: 'shield',   label: 'SSL Certificate ฟรี' },
];

const BONUSES = [
    'SSL Certificate 1 ปี ฟรี',
    'cPanel 1 GB, Thai LiteSpeed',
    'Service Banner (1 ชิ้น)',
    'ฟรีค่า Revise ใน 30 วัน',
    'Google Analytics Setup',
];

const ICON_PATHS = {
    design:   'M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42',
    edit:     'M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10',
    support:  'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z',
    bolt:     'M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z',
    globe:    'M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418',
    academic: 'M4.26 10.147a60.436 60.436 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.905 59.905 0 0 1 12 3.493a59.902 59.902 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5',
    mobile:   'M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18h3',
    shield:   'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z',
    star:     'M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z',
    clock:    'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
    chart:    'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z',
};
</script>

<template>
    <PublicLayout>

        <!-- ══════════════════════════════════════════════════════
             PAGE HEADER
        ══════════════════════════════════════════════════════ -->
        <section class="bg-white pt-10 pb-6 border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <nav class="flex items-center gap-2 text-xs text-gray-400 font-kanit mb-4">
                    <Link href="/" class="hover:text-gray-600">หน้าแรก</Link>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                    <span class="text-gray-600">บริการของเรา</span>
                </nav>
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 font-kanit">รับทำเว็บไซต์</h1>
                <p class="mt-2 text-gray-500 font-kanit text-sm">เลือกแพ็กเกจที่เหมาะกับธุรกิจของคุณ ทีมมืออาชีพพร้อมดูแลทุกขั้นตอน</p>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             ALTERNATING PACKAGE DETAIL CARDS
        ══════════════════════════════════════════════════════ -->
        <section class="bg-white">
            <div class="max-w-7xl mx-auto">
                <div v-for="(pkg, i) in packages" :key="pkg.id"
                     class="grid lg:grid-cols-2 border-b border-gray-100 last:border-0">

                    <!-- Image side -->
                    <div class="relative overflow-hidden"
                         :class="i % 2 === 0 ? 'lg:order-first' : 'lg:order-last'">
                        <div class="aspect-[4/3] lg:aspect-auto lg:h-full min-h-[300px] relative">
                            <img :src="pkg.cover_image || PACKAGE_IMAGES[i % PACKAGE_IMAGES.length]"
                                 :alt="pkg.name"
                                 class="w-full h-full object-cover"/>
                            <div class="absolute bottom-4 left-4 bg-white rounded-xl shadow-lg px-4 py-2.5">
                                <p class="text-xs text-gray-400 font-kanit">ราคาเริ่มต้น</p>
                                <p class="text-xl font-black text-gray-900 font-kanit leading-tight">
                                    {{ formatPrice(pkg.price) }}
                                    <span class="text-sm font-normal text-gray-500"> ฿</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Content side -->
                    <div class="px-8 lg:px-14 py-10 lg:py-14 flex flex-col justify-center"
                         :class="i % 2 === 0 ? 'lg:order-last' : 'lg:order-first'">
                        <div class="mb-4">
                            <span v-if="pkg.tag_text"
                                  class="inline-block text-xs font-bold uppercase tracking-widest mb-2 font-kanit"
                                  :style="`color: ${pkg.color_from ?? '#7c1d1d'}`">
                                {{ pkg.tag_text }}
                            </span>
                            <h2 class="text-2xl lg:text-3xl font-bold font-kanit leading-snug text-gray-900">
                                {{ pkg.name }}
                            </h2>
                            <div class="flex items-baseline gap-1.5 mt-2">
                                <span class="text-3xl font-black text-gray-900 font-kanit">{{ formatPrice(pkg.price) }}</span>
                                <span class="text-gray-500 font-kanit text-sm">฿</span>
                                <span class="text-xs text-gray-400 font-kanit ml-1">*ยังไม่รวม VAT</span>
                            </div>
                        </div>

                        <p v-if="pkg.description" class="text-gray-500 font-kanit text-sm leading-relaxed mb-5">
                            {{ pkg.description }}
                        </p>

                        <ul v-if="pkg.features?.length" class="space-y-2 mb-7">
                            <li v-for="f in pkg.features.filter(f => f.type === 'included').slice(0, 8)"
                                :key="f.id"
                                class="flex items-start gap-2.5 font-kanit text-sm">
                                <span class="w-4 h-4 rounded-full flex-shrink-0 mt-0.5 flex items-center justify-center"
                                      :style="`background: ${pkg.color_from ?? '#7c1d1d'}22`">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="3"
                                         :style="`color: ${pkg.color_from ?? '#7c1d1d'}`" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                    </svg>
                                </span>
                                <span class="text-gray-700 leading-snug">{{ f.title }}</span>
                            </li>
                        </ul>

                        <div class="flex flex-wrap items-center gap-3">
                            <a :href="pkg.cta_primary_url ?? '/contact'"
                               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-sm font-bold text-white transition hover:opacity-90 font-kanit shadow"
                               :style="`background: linear-gradient(135deg, ${pkg.color_from ?? '#7c1d1d'}, ${pkg.color_to ?? '#b91c1c'})`">
                                {{ pkg.cta_primary_text ?? 'สั่งซื้อเลย' }}
                            </a>
                            <a :href="pkg.cta_secondary_url ?? '/portfolio'"
                               class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-sm font-semibold border-2 transition hover:bg-gray-50 font-kanit"
                               :style="`border-color: ${pkg.color_from ?? '#7c1d1d'}; color: ${pkg.color_from ?? '#7c1d1d'}`">
                                {{ pkg.cta_secondary_text ?? 'ดูตัวอย่างงาน' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             BONUSES + PRICING COMPARISON
        ══════════════════════════════════════════════════════ -->
        <section class="py-14 bg-gray-50">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="grid lg:grid-cols-3 gap-8">

                    <!-- Bonuses card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-7">
                        <div class="flex items-center gap-2.5 mb-5">
                            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9.375 3a1.875 1.875 0 0 0 0 3.75h1.875v4.5H3.375A1.875 1.875 0 0 1 1.5 9.375v-.75c0-1.036.84-1.875 1.875-1.875h3.193A3.375 3.375 0 0 1 12 2.753a3.375 3.375 0 0 1 5.432 3.997h3.943c1.035 0 1.875.84 1.875 1.875v.75c0 1.036-.84 1.875-1.875 1.875H12.75v-4.5h1.875a1.875 1.875 0 1 0-1.875-1.875V6.75h-1.5V4.875C11.25 3.839 10.41 3 9.375 3ZM11.25 12.75H3v6.75a2.25 2.25 0 0 0 2.25 2.25h6v-9ZM12.75 12.75v9h6.75A2.25 2.25 0 0 0 21.75 19.5v-6.75h-9Z"/>
                                </svg>
                            </div>
                            <h3 class="font-bold text-gray-900 font-kanit">ของแถมทุก Package</h3>
                        </div>
                        <ul class="space-y-3">
                            <li v-for="b in BONUSES" :key="b"
                                class="flex items-center gap-2.5 text-sm font-kanit text-gray-600">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                </svg>
                                {{ b }}
                            </li>
                        </ul>
                    </div>

                    <!-- Pricing comparison -->
                    <div class="lg:col-span-2">
                        <h3 class="text-xl font-bold text-gray-900 font-kanit mb-4">เปรียบเทียบราคา</h3>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div v-for="pkg in packages" :key="`price-${pkg.id}`"
                                 class="rounded-xl p-5 text-white shadow-md"
                                 :style="`background: linear-gradient(135deg, ${pkg.color_from ?? '#7c1d1d'}, ${pkg.color_to ?? '#b91c1c'})`">
                                <p class="text-xs font-semibold text-white/70 font-kanit mb-1">{{ pkg.tag_text }}</p>
                                <h4 class="font-bold font-kanit leading-snug mb-2">{{ pkg.name }}</h4>
                                <p class="text-2xl font-black font-kanit">{{ formatPrice(pkg.price) }} ฿</p>
                                <p class="text-xs text-white/60 font-kanit mt-0.5">*ยังไม่รวม VAT 7%</p>
                                <a :href="pkg.cta_primary_url ?? '/contact'"
                                   class="mt-4 block w-full text-center bg-white/20 hover:bg-white/30 text-white text-xs font-bold py-2 rounded-lg transition font-kanit">
                                    {{ pkg.cta_primary_text ?? 'สั่งซื้อเลย' }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             CONTACT STRIP
        ══════════════════════════════════════════════════════ -->
        <section class="bg-gray-900 py-5">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-wrap items-center justify-center gap-6 lg:gap-12 font-kanit">
                <a v-if="site.phone" :href="`tel:${site.phone}`"
                   class="flex items-center gap-3 text-white hover:text-green-400 transition">
                    <div class="w-9 h-9 rounded-full bg-green-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.338c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 4c.828 0 1.655.063 2.465.188C9.65 4.371 10.5 5.4 10.5 6.584v3.33c0 1.007-.603 1.927-1.548 2.308L7.3 12.92c.618 1.3 1.556 2.455 2.72 3.32l.703-1.65c.38-.946 1.3-1.549 2.308-1.549h3.33c1.184 0 2.213.85 2.396 2.035.125.81.188 1.637.188 2.465 0 .569-.044 1.127-.132 1.668-.09 1.174-1.076 2.085-2.254 2.085C9.11 22.5 1.5 14.89 1.5 5.5c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 3"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">โทรศัพท์</p>
                        <p class="font-bold">{{ site.phone }}</p>
                    </div>
                </a>
                <div class="hidden lg:block h-8 w-px bg-gray-700"/>
                <a href="https://line.me/ti/p/@dosc" target="_blank" rel="noopener"
                   class="flex items-center gap-3 text-white hover:text-green-300 transition">
                    <div class="w-9 h-9 rounded-full bg-green-400/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-400" viewBox="0 0 24 24" fill="currentColor">
                            <path d="m12 .5c-6.615 0-12 4.398-12 9.803 0 4.841 4.27 8.897 10.035 9.608.391.084.922.258 1.057.593.12.301.079.771.038 1.073l-.164 1.013c-.045.301-.24 1.186 1.049.647 1.291-.539 6.916-4.103 9.436-7.023 1.724-1.952 2.549-3.965 2.549-6.311 0-5.405-5.385-9.803-12-9.803z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">LINE</p>
                        <p class="font-bold">@doscgroup</p>
                    </div>
                </a>
                <div class="hidden lg:block h-8 w-px bg-gray-700"/>
                <a href="/contact"
                   class="px-6 py-2.5 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-full transition font-kanit shadow-md">
                    ปรึกษาฟรี
                </a>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             WHAT EVERY PACKAGE INCLUDES
        ══════════════════════════════════════════════════════ -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-10">
                    <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 font-kanit">ทุกแพ็กเกจคุณจะได้รับ</h2>
                    <p class="mt-2 text-gray-500 font-kanit text-sm">สิ่งเหล่านี้รวมอยู่ในทุก Package ไม่ว่าคุณจะเลือกแพ็กเกจไหน</p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div v-for="b in EVERY_PKG_BENEFITS" :key="b.label"
                         class="flex flex-col items-center text-center p-5 rounded-2xl border border-gray-100 hover:border-red-100 hover:shadow-sm transition group">
                        <div class="w-12 h-12 rounded-xl bg-red-50 group-hover:bg-red-100 flex items-center justify-center mb-3 transition">
                            <svg class="w-6 h-6 text-red-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="ICON_PATHS[b.icon]"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-800 font-kanit leading-snug">{{ b.label }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             GUARANTEE BANNER
        ══════════════════════════════════════════════════════ -->
        <section class="bg-gradient-to-r from-red-800 to-red-700 py-8">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-center sm:text-left">
                    <p class="text-white font-bold text-lg font-kanit leading-snug">
                        รับประกันว่าคุณจะได้เว็บไซต์ภายใน 5–7 วันทำการ
                    </p>
                    <p class="text-white/75 text-sm font-kanit mt-1">
                        มีทีมงานดูแลตลอดระยะเวลารับประกัน 1 ปี ไม่มีค่าใช้จ่ายซ่อนเร้น
                    </p>
                </div>
                <a href="/contact"
                   class="flex-shrink-0 px-7 py-3 bg-white text-red-800 font-bold rounded-full text-sm hover:bg-gray-50 transition font-kanit shadow-md">
                    สอบถามรายละเอียด
                </a>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             ADD-ONS
        ══════════════════════════════════════════════════════ -->
        <section v-if="addons.length" class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-10">
                    <p class="text-xs font-bold tracking-widest uppercase text-red-700 mb-2 font-kanit">ADD-ONS</p>
                    <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 font-kanit">บริการเสริมเพิ่มเติม</h2>
                    <p class="mt-2 text-gray-500 font-kanit text-sm">เพิ่มเติมได้กับทุก Package ตามความต้องการ</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="addon in addons" :key="addon.id"
                         class="bg-white rounded-xl border border-gray-100 p-5 hover:shadow-md transition hover:border-red-100 group">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="font-semibold text-gray-900 font-kanit text-sm leading-snug">{{ addon.name }}</h3>
                            <span class="flex-shrink-0 text-sm font-black text-red-800 font-kanit whitespace-nowrap">
                                +{{ formatPrice(addon.price) }} ฿
                            </span>
                        </div>
                        <p v-if="addon.description" class="mt-2 text-xs text-gray-500 font-kanit leading-relaxed">
                            {{ addon.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             PORTFOLIO SAMPLES
        ══════════════════════════════════════════════════════ -->
        <section v-if="portfolios.length" class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex items-end justify-between mb-10">
                    <div>
                        <p class="text-xs font-bold tracking-widest uppercase text-red-700 mb-2 font-kanit">OUR WORK</p>
                        <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 font-kanit">ผลงานตัวอย่าง</h2>
                    </div>
                    <Link href="/portfolio"
                          class="hidden sm:flex items-center gap-1.5 text-sm font-semibold text-red-700 hover:text-red-900 font-kanit">
                        ดูทั้งหมด
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                        </svg>
                    </Link>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 lg:gap-4">
                    <a v-for="p in portfolios" :key="p.id"
                       :href="p.client_url && p.client_url !== '#' ? p.client_url : '/portfolio'"
                       :target="p.client_url && p.client_url !== '#' ? '_blank' : undefined"
                       rel="noopener noreferrer"
                       class="group relative rounded-xl overflow-hidden aspect-[4/3] bg-gray-100 shadow-sm hover:shadow-lg transition duration-300">
                        <img v-if="p.thumbnail_url" :src="p.thumbnail_url" :alt="p.title"
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"/>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col justify-end p-4">
                            <p class="text-white font-semibold text-sm font-kanit">{{ p.title }}</p>
                            <p v-if="p.package_name" class="text-white/60 text-xs font-kanit">{{ p.package_name }}</p>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             WORK PROCESS
        ══════════════════════════════════════════════════════ -->
        <section v-if="workSteps.length" class="bg-gray-50 overflow-hidden">
            <div class="max-w-7xl mx-auto">
                <div class="grid lg:grid-cols-2">

                    <!-- Left: dark red panel -->
                    <div class="bg-[#7c1d1d] px-10 lg:px-14 py-16 flex flex-col justify-center">
                        <p class="text-xs font-bold tracking-widest uppercase text-white/50 mb-3 font-kanit">HOW WE WORK</p>
                        <h2 class="text-2xl lg:text-3xl font-bold text-white font-kanit leading-snug mb-4">
                            สร้างเว็บไซต์ที่ดีที่สุด<br>สำหรับธุรกิจของคุณ<br>ใน {{ workSteps.length }} ขั้นตอน
                        </h2>
                        <ul class="space-y-3 mb-8">
                            <li v-for="step in workSteps" :key="step.id"
                                class="flex items-center gap-3 text-white/80 font-kanit text-sm">
                                <span class="w-6 h-6 rounded-full bg-white/20 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">
                                    {{ step.step_number }}
                                </span>
                                {{ step.title }}
                            </li>
                        </ul>
                        <a href="/contact"
                           class="self-start flex items-center gap-2 px-6 py-3 bg-white text-red-800 font-bold rounded-full text-sm hover:bg-gray-100 transition font-kanit">
                            เริ่มโปรเจกต์กับเรา
                        </a>
                    </div>

                    <!-- Right: step image grid -->
                    <div class="grid grid-cols-2">
                        <div v-for="(step, i) in workSteps.slice(0, 4)" :key="`img-${step.id}`"
                             class="relative overflow-hidden aspect-square">
                            <img :src="step.image_url || STEP_IMAGES[i % STEP_IMAGES.length]"
                                 :alt="step.title"
                                 class="w-full h-full object-cover"/>
                            <div class="absolute inset-0 bg-black/40 flex flex-col justify-end p-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-full bg-red-700 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">
                                        {{ step.step_number }}
                                    </span>
                                    <p class="text-white text-xs font-bold font-kanit leading-snug">{{ step.title }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             WHY CHOOSE US
        ══════════════════════════════════════════════════════ -->
        <section v-if="whyUsItems.length" class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-10">
                    <p class="text-xs font-bold tracking-widest uppercase text-red-700 mb-2 font-kanit">WHY US</p>
                    <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 font-kanit">ทำไมต้องเลือกเรา</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div v-for="item in whyUsItems" :key="item.id"
                         class="flex gap-4 p-5 rounded-xl border border-gray-100 hover:border-red-100 hover:shadow-sm transition group">
                        <div class="w-10 h-10 rounded-xl bg-red-50 group-hover:bg-red-100 flex items-center justify-center flex-shrink-0 transition">
                            <svg class="w-5 h-5 text-red-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      :d="ICON_PATHS[item.icon_name] ?? ICON_PATHS.star"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 font-kanit text-sm mb-1">{{ item.title }}</h3>
                            <p class="text-xs text-gray-500 font-kanit leading-relaxed">{{ item.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             FREE CONSULTATION CTA
        ══════════════════════════════════════════════════════ -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-xl mx-auto px-6 text-center">
                <img v-if="site.logo_url" :src="site.logo_url" :alt="site.site_name"
                     class="h-12 w-auto object-contain mx-auto mb-6"/>
                <p v-else class="text-xl font-bold text-gray-900 font-kanit mb-6">{{ site.site_name ?? 'DOSC Group' }}</p>

                <h2 class="text-2xl font-bold text-gray-900 font-kanit mb-2">เราพร้อมให้คำปรึกษาฟรี</h2>
                <p class="text-gray-500 font-kanit text-sm mb-8">ทีมงานพร้อมตอบคำถามและแนะนำ Package ที่เหมาะกับธุรกิจของคุณ</p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a v-if="site.phone" :href="`tel:${site.phone}`"
                       class="w-full sm:w-auto flex items-center justify-center gap-2.5 px-7 py-3 bg-gray-900 text-white font-bold rounded-full hover:bg-gray-800 transition font-kanit text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.338c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 4c.828 0 1.655.063 2.465.188C9.65 4.371 10.5 5.4 10.5 6.584v3.33c0 1.007-.603 1.927-1.548 2.308L7.3 12.92c.618 1.3 1.556 2.455 2.72 3.32l.703-1.65c.38-.946 1.3-1.549 2.308-1.549h3.33c1.184 0 2.213.85 2.396 2.035.125.81.188 1.637.188 2.465 0 .569-.044 1.127-.132 1.668-.09 1.174-1.076 2.085-2.254 2.085C9.11 22.5 1.5 14.89 1.5 5.5c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 3"/>
                        </svg>
                        {{ site.phone ?? 'โทรหาเรา' }}
                    </a>
                    <a href="https://line.me/ti/p/@dosc" target="_blank" rel="noopener"
                       class="w-full sm:w-auto flex items-center justify-center gap-2.5 px-7 py-3 bg-green-500 text-white font-bold rounded-full hover:bg-green-600 transition font-kanit text-sm">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="m12 .5c-6.615 0-12 4.398-12 9.803 0 4.841 4.27 8.897 10.035 9.608.391.084.922.258 1.057.593.12.301.079.771.038 1.073l-.164 1.013c-.045.301-.24 1.186 1.049.647 1.291-.539 6.916-4.103 9.436-7.023 1.724-1.952 2.549-3.965 2.549-6.311 0-5.405-5.385-9.803-12-9.803z"/>
                        </svg>
                        LINE @doscgroup
                    </a>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             DARK FOOTER CTA
        ══════════════════════════════════════════════════════ -->
        <section class="relative overflow-hidden bg-gray-950 py-20 lg:py-28">
            <img src="https://images.unsplash.com/photo-1505409628601-edc9af17fda6?w=1600&h=700&fit=crop&q=40"
                 alt=""
                 class="absolute inset-0 w-full h-full object-cover opacity-10 pointer-events-none"/>
            <div class="relative z-10 max-w-3xl mx-auto px-6 text-center">
                <p class="text-white/50 text-sm font-kanit mb-4 tracking-widest uppercase">DOSC Group</p>
                <h2 class="text-3xl lg:text-5xl font-black text-white font-kanit leading-snug mb-6">
                    ทำเว็บไซต์ให้เป็นเรื่อง<span class="text-red-400">ง่าย</span>สำหรับคุณ
                </h2>
                <p class="text-white/60 font-kanit mb-10 text-base">
                    ทีมงานมืออาชีพพร้อมสร้างเว็บไซต์ที่ตอบโจทย์ธุรกิจของคุณ<br class="hidden sm:block"/>
                    ราคาโปร่งใส ส่งงานตรงเวลา รับประกัน 1 ปี
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="/contact"
                       class="w-full sm:w-auto px-10 py-3.5 bg-red-700 hover:bg-red-600 text-white font-bold rounded-full text-sm transition font-kanit shadow-xl">
                        เริ่มต้นโปรเจกต์ฟรี
                    </a>
                    <a v-if="site.phone" :href="`tel:${site.phone}`"
                       class="w-full sm:w-auto px-10 py-3.5 border-2 border-white/30 text-white hover:bg-white/10 font-bold rounded-full text-sm transition font-kanit">
                        {{ site.phone }}
                    </a>
                </div>
            </div>
        </section>

    </PublicLayout>
</template>
