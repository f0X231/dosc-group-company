<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    hero:                { type: Object, default: null },
    services:            { type: Array,  default: () => [] },
    portfolios:          { type: Array,  default: () => [] },
    packages:            { type: Array,  default: () => [] },
    workSteps:           { type: Array,  default: () => [] },
    whyUsItems:          { type: Array,  default: () => [] },
    testimonialSettings: { type: Object, default: () => ({}) },
    testimonials:        { type: Array,  default: () => [] },
    partnerSettings:     { type: Object, default: () => ({}) },
    partners:            { type: Array,  default: () => [] },
    faqs:                { type: Array,  default: () => [] },
    latestBlogs:         { type: Array,  default: () => [] },
});

const page = usePage();
const site = computed(() => page.props.site ?? {});

// ─── Animation ────────────────────────────────────────────────────────────────
const animated       = ref(false);
const typedText      = ref('');
const showCursor     = ref(true);

function runTypewriter(text) {
    let i = 0;
    const interval = setInterval(() => {
        typedText.value = text.slice(0, ++i);
        if (i >= text.length) {
            clearInterval(interval);
            setInterval(() => { showCursor.value = !showCursor.value; }, 530);
        }
    }, 80);
}

// ─── Computed styles ─────────────────────────────────────────────────────────
const hero = computed(() => props.hero);

const heroBg = computed(() => {
    if (!hero.value) return 'background: linear-gradient(135deg, #f1f1f1, #e5e5e5)';
    if (hero.value.bg_type === 'solid') return `background-color: ${hero.value.bg_color_from}`;
    return `background: linear-gradient(135deg, ${hero.value.bg_color_from}, ${hero.value.bg_color_to})`;
});

const overlayStyle = computed(() => {
    if (!hero.value) return '';
    const hex  = hero.value.overlay_color ?? '#000000';
    const r    = parseInt(hex.slice(1, 3), 16);
    const g    = parseInt(hex.slice(3, 5), 16);
    const b    = parseInt(hex.slice(5, 7), 16);
    const op   = (hero.value.overlay_opacity ?? 40) / 100;
    return `background: rgba(${r},${g},${b},${op})`;
});

const isDark   = computed(() => !hero.value || hero.value.text_color === 'dark');
const animMode = computed(() => hero.value?.animation_style ?? 'slide_up');

const imgSizeClass = computed(() => ({
    sm: 'w-1/4 lg:w-1/3',
    md: 'w-1/3 lg:w-2/5',
    lg: 'w-2/5 lg:w-1/2',
}[hero.value?.image_size ?? 'md']));

const imgAlignClass = computed(() => ({
    top:    'items-start',
    center: 'items-center',
    bottom: 'items-end',
}[hero.value?.image_align_y ?? 'center']));

// Headline highlight: typewriter vs normal
const displayHighlight = computed(() => {
    if (animMode.value === 'typewriter') return typedText.value;
    return hero.value?.headline_highlight ?? '';
});

// ─── Testimonials carousel ────────────────────────────────────────────────────
const activeSlide   = ref(0);
let   autoplayTimer = null;

const testimonialCount = computed(() => props.testimonials.length);

function goSlide(i) {
    activeSlide.value = (i + testimonialCount.value) % testimonialCount.value;
}
function prevSlide() { goSlide(activeSlide.value - 1); }
function nextSlide() { goSlide(activeSlide.value + 1); }

function startAutoplay() {
    if (testimonialCount.value < 2) return;
    autoplayTimer = setInterval(() => nextSlide(), 5000);
}
function stopAutoplay() { clearInterval(autoplayTimer); }

onMounted(() => {
    requestAnimationFrame(() => { animated.value = true; });
    if (props.hero?.animation_style === 'typewriter' && props.hero?.headline_highlight) {
        runTypewriter(props.hero.headline_highlight);
    }
    startAutoplay();
});
onUnmounted(() => stopAutoplay());

function initialChar(name) { return name?.charAt(0)?.toUpperCase() ?? '?'; }
const avatarStack = computed(() => props.testimonials.slice(0, 4));

// ─── Partners marquee ─────────────────────────────────────────────────────────
const marqueeItems = computed(() => {
    if (props.partners.length === 0) return [];
    const copies = Math.max(2, Math.ceil(12 / props.partners.length) + 1);
    return Array.from({ length: copies }, () => props.partners).flat();
});

// ─── Icon paths ───────────────────────────────────────────────────────────────
const ICON_PATHS = {
    globe:   'M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418',
    cart:    'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z',
    chart:   'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z',
    code:    'M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5',
    design:  'M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42',
    rocket:  'M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z',
    shield:  'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z',
    support: 'M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z',
    chat:    'M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155',
    star:    'M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z',
    clock:   'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
};

// ─── FAQ accordion ────────────────────────────────────────────────────────────
const openFaqId = ref(null);
function toggleFaq(id) {
    openFaqId.value = openFaqId.value === id ? null : id;
}

function formatPrice(n) {
    return n?.toLocaleString('th-TH');
}
</script>

<template>
    <Head title="หน้าแรก" />
    <PublicLayout>

        <!-- ══════════════════════════════════════════════════════
             HERO BANNER
        ══════════════════════════════════════════════════════ -->
        <section class="relative overflow-hidden min-h-[85vh] flex flex-col justify-center font-kanit"
                 :style="heroBg">

            <!-- Background: video -->
            <template v-if="hero?.media_type === 'video' && hero.media_url">
                <video autoplay muted loop playsinline
                       class="absolute inset-0 w-full h-full object-cover">
                    <source :src="hero.media_url" type="video/mp4"/>
                </video>
                <div class="absolute inset-0" :style="overlayStyle"></div>
            </template>

            <!-- Background: image_full -->
            <template v-else-if="hero?.media_type === 'image_full' && hero.media_url">
                <img :src="hero.media_url" alt="" class="absolute inset-0 w-full h-full object-cover"/>
                <div class="absolute inset-0" :style="overlayStyle"></div>
            </template>

            <!-- Animated SVG wave lines (background decoration) -->
            <svg v-if="!hero?.media_url || hero?.media_type === 'image_side' || hero?.media_type === 'none'"
                 class="absolute inset-0 w-full h-full opacity-20 pointer-events-none"
                 viewBox="0 0 1440 600" preserveAspectRatio="xMidYMid slice"
                 xmlns="http://www.w3.org/2000/svg">
                <path class="wave-path" d="M0,300 C360,100 720,500 1440,300" stroke="currentColor" fill="none" stroke-width="1"/>
                <path class="wave-path wave-path-2" d="M0,350 C400,150 900,450 1440,250" stroke="currentColor" fill="none" stroke-width="1"/>
                <path class="wave-path wave-path-3" d="M0,250 C300,50 800,550 1440,350" stroke="currentColor" fill="none" stroke-width="1"/>
            </svg>

            <!-- Content -->
            <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 w-full py-20">

                <!-- image_side layout -->
                <div v-if="hero?.media_type === 'image_side'"
                     class="flex gap-8 lg:gap-16"
                     :class="[
                         hero.image_position === 'left' ? 'flex-col-reverse md:flex-row-reverse' : 'flex-col-reverse md:flex-row',
                         imgAlignClass,
                     ]">

                    <!-- Text -->
                    <div class="flex-1">
                        <HeroText :hero="hero" :animated="animated" :anim-mode="animMode"
                                  :is-dark="isDark" :display-highlight="displayHighlight"
                                  :show-cursor="showCursor"/>
                    </div>

                    <!-- Illustration -->
                    <div :class="['flex-shrink-0 flex', imgAlignClass, imgSizeClass]">
                        <img :src="hero.media_url" :alt="hero.headline"
                             class="w-full h-auto object-contain drop-shadow-xl float-anim"/>
                    </div>
                </div>

                <!-- Full bg layout (video / image_full / none) -->
                <div v-else class="max-w-2xl">
                    <HeroText :hero="hero" :animated="animated" :anim-mode="animMode"
                              :is-dark="isDark" :display-highlight="displayHighlight"
                              :show-cursor="showCursor"/>
                </div>
            </div>

            <!-- Stats bar -->
            <div v-if="hero?.stats?.length"
                 class="relative z-10 border-t"
                 :class="isDark ? 'border-black/10' : 'border-white/20'">
                <div class="max-w-7xl mx-auto px-6 lg:px-8 py-6 flex gap-8 lg:gap-16 flex-wrap">
                    <div v-for="(stat, i) in hero.stats" :key="i"
                         class="stat-item"
                         :class="animated ? 'stat-visible' : ''"
                         :style="`animation-delay: ${0.6 + i * 0.1}s`">
                        <p class="text-2xl lg:text-3xl font-bold"
                           :class="isDark ? 'text-gray-900' : 'text-white'">
                            {{ stat.value }}<span class="text-green-500">{{ stat.suffix }}</span>
                        </p>
                        <p class="text-sm mt-0.5"
                           :class="isDark ? 'text-gray-500' : 'text-white/70'">
                            {{ stat.label }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Placeholder (no banner set) -->
            <div v-if="!hero" class="relative z-10 max-w-7xl mx-auto px-6 py-24 text-center">
                <p class="text-4xl font-bold text-gray-400">Hero Banner</p>
                <p class="mt-2 text-gray-400 text-sm">ยังไม่ได้ตั้งค่า — ไปที่ Admin → Hero Banner</p>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             SERVICES (บริการของเรา)
        ══════════════════════════════════════════════════════ -->
        <section v-if="services.length" class="py-16 lg:py-20 bg-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-12">
                    <p class="text-xs font-bold tracking-widest uppercase text-red-700 mb-2">OUR SERVICES</p>
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 font-kanit">บริการของเรา</h2>
                    <p class="mt-3 text-gray-500 text-base font-kanit max-w-xl mx-auto">ทีมงานมืออาชีพพร้อมดูแลคุณตั้งแต่ต้นจนจบโปรเจกต์</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="s in services" :key="s.id"
                         class="group relative bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <!-- Image -->
                        <div class="relative h-48 overflow-hidden bg-gray-50">
                            <img v-if="s.image_url" :src="s.image_url" :alt="s.title"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"/>
                            <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-red-50 to-red-100">
                                <svg class="w-16 h-16 text-red-300" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" :d="ICON_PATHS[s.icon_name] ?? ICON_PATHS.globe"/>
                                </svg>
                            </div>
                            <!-- Badge -->
                            <span v-if="s.badge_text"
                                  class="absolute top-3 left-3 px-2.5 py-1 text-xs font-semibold text-white rounded-full font-kanit shadow"
                                  :style="`background:${s.badge_color ?? '#7c1d1d'}`">{{ s.badge_text }}</span>
                        </div>
                        <!-- Body -->
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-red-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" :d="ICON_PATHS[s.icon_name] ?? ICON_PATHS.globe"/>
                                    </svg>
                                </div>
                                <h3 class="font-bold text-gray-900 font-kanit text-sm leading-snug">{{ s.title }}</h3>
                            </div>
                            <p v-if="s.subtitle" class="text-xs text-gray-500 font-kanit leading-relaxed mb-4">{{ s.subtitle }}</p>
                            <a v-if="s.cta_text" :href="s.cta_url ?? '#'"
                               class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 hover:text-red-900 font-kanit group/link">
                                {{ s.cta_text }}
                                <svg class="w-3.5 h-3.5 transition-transform group-hover/link:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                                </svg>
                            </a>
                        </div>
                        <!-- Hover accent line -->
                        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-red-700 scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"/>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             PORTFOLIO (ผลงานของเรา)
        ══════════════════════════════════════════════════════ -->
        <section v-if="portfolios.length" class="py-16 lg:py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex items-end justify-between mb-10">
                    <div>
                        <p class="text-xs font-bold tracking-widest uppercase text-red-700 mb-2">OUR WORK</p>
                        <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 font-kanit">ผลงานของเรา</h2>
                    </div>
                    <Link href="/portfolio"
                          class="hidden sm:flex items-center gap-1.5 text-sm font-semibold text-red-700 hover:text-red-900 font-kanit transition">
                        ดูทั้งหมด
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                        </svg>
                    </Link>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 lg:gap-4">
                    <a v-for="p in portfolios" :key="p.id"
                       :href="p.client_url ?? '#'"
                       :target="p.client_url && p.client_url !== '#' ? '_blank' : undefined"
                       rel="noopener noreferrer"
                       class="group relative rounded-xl overflow-hidden aspect-[4/3] bg-gray-200 shadow-sm hover:shadow-lg transition-all duration-300">
                        <img v-if="p.thumbnail_url" :src="p.thumbnail_url" :alt="p.title"
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"/>
                        <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                            </svg>
                        </div>
                        <!-- Hover overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                            <p class="text-white font-semibold text-sm font-kanit leading-snug">{{ p.title }}</p>
                            <p v-if="p.package_name" class="text-white/60 text-xs font-kanit mt-0.5">{{ p.package_name }}</p>
                            <div class="flex items-center gap-1 mt-2">
                                <svg class="w-3.5 h-3.5 text-white/80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                </svg>
                                <span class="text-white/80 text-xs font-kanit">เยี่ยมชมเว็บไซต์</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="text-center mt-8 sm:hidden">
                    <Link href="/portfolio" class="text-sm font-semibold text-red-700 hover:text-red-900 font-kanit">
                        ดูผลงานทั้งหมด →
                    </Link>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             PACKAGES (แพ็กเกจของเรา)
        ══════════════════════════════════════════════════════ -->
        <section v-if="packages.length" class="py-16 lg:py-24 bg-[#7c1d1d]">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-12">
                    <p class="text-xs font-bold tracking-widest uppercase text-white/50 mb-2">PACKAGES</p>
                    <h2 class="text-3xl lg:text-4xl font-bold text-white font-kanit">แพ็กเกจของเรา</h2>
                    <p class="mt-3 text-white/70 font-kanit">เลือก package ที่เหมาะกับธุรกิจของคุณ ราคายังไม่รวม VAT 7%</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div v-for="pkg in packages" :key="pkg.id"
                         class="relative flex flex-col rounded-2xl overflow-hidden shadow-xl"
                         :style="`background: linear-gradient(160deg, ${pkg.color_from ?? '#1d4ed8'}, ${pkg.color_to ?? '#3b82f6'})`">
                        <!-- Badge -->
                        <div v-if="pkg.badge_text"
                             class="absolute top-4 right-4 bg-white/20 backdrop-blur-sm px-2.5 py-1 rounded-full text-xs font-bold text-white font-kanit">
                            {{ pkg.badge_text }}
                        </div>
                        <!-- Header -->
                        <div class="px-6 pt-7 pb-5">
                            <p v-if="pkg.tag_text" class="text-xs font-semibold text-white/60 uppercase tracking-wider mb-2 font-kanit">{{ pkg.tag_text }}</p>
                            <h3 class="text-lg font-bold text-white font-kanit leading-snug">{{ pkg.name }}</h3>
                            <div class="mt-4 flex items-baseline gap-1">
                                <span class="text-3xl font-black text-white">{{ formatPrice(pkg.price) }}</span>
                                <span class="text-white/70 text-sm font-kanit">บาท</span>
                            </div>
                            <p v-if="pkg.is_vat_excluded" class="text-white/50 text-xs mt-1 font-kanit">*ยังไม่รวม VAT 7%</p>
                        </div>
                        <!-- Divider -->
                        <div class="mx-6 h-px bg-white/20"/>
                        <!-- Features -->
                        <ul class="flex-1 px-6 py-5 space-y-2.5">
                            <li v-for="f in pkg.features" :key="f.id"
                                class="flex items-start gap-2 text-sm text-white/90 font-kanit">
                                <svg class="w-4 h-4 text-white flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                </svg>
                                {{ f.title }}
                            </li>
                        </ul>
                        <!-- CTAs -->
                        <div class="px-6 pb-7 pt-3 flex flex-col gap-2">
                            <a v-if="pkg.cta_primary_text" :href="pkg.cta_primary_url ?? '/contact'"
                               class="block w-full text-center py-2.5 bg-white font-bold text-sm rounded-xl transition hover:bg-white/90 font-kanit"
                               :style="`color: ${pkg.color_from ?? '#1d4ed8'}`">
                                {{ pkg.cta_primary_text }}
                            </a>
                            <a v-if="pkg.cta_secondary_text" :href="pkg.cta_secondary_url ?? '/portfolio'"
                               class="block w-full text-center py-2 text-white/80 text-xs hover:text-white transition font-kanit">
                                {{ pkg.cta_secondary_text }}
                            </a>
                        </div>
                    </div>
                </div>
                <p class="text-center text-white/50 text-xs font-kanit mt-6">
                    ต้องการ Custom Package? <a href="/contact" class="text-white/80 underline hover:text-white">ติดต่อเราได้เลย</a>
                </p>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             CONTACT STRIP (ติดต่อสอบถาม)
        ══════════════════════════════════════════════════════ -->
        <section class="bg-gray-900 py-5">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-wrap items-center justify-center gap-6 lg:gap-12 font-kanit">
                <a v-if="site.phone" :href="`tel:${site.phone}`"
                   class="flex items-center gap-2.5 text-white hover:text-green-400 transition group">
                    <div class="w-9 h-9 rounded-full bg-white/10 group-hover:bg-green-500/20 flex items-center justify-center transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.338c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 4c.828 0 1.655.063 2.465.188C9.65 4.371 10.5 5.4 10.5 6.584v3.33c0 1.007-.603 1.927-1.548 2.308L7.3 12.92c.618 1.3 1.556 2.455 2.72 3.32l.703-1.65c.38-.946 1.3-1.549 2.308-1.549h3.33c1.184 0 2.213.85 2.396 2.035.125.81.188 1.637.188 2.465 0 .569-.044 1.127-.132 1.668-.09 1.174-1.076 2.085-2.254 2.085C9.11 22.5 1.5 14.89 1.5 5.5c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 3"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">โทรศัพท์</p>
                        <p class="font-semibold text-sm">{{ site.phone }}</p>
                    </div>
                </a>
                <a v-if="site.email" :href="`mailto:${site.email}`"
                   class="flex items-center gap-2.5 text-white hover:text-blue-400 transition group">
                    <div class="w-9 h-9 rounded-full bg-white/10 group-hover:bg-blue-500/20 flex items-center justify-center transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">อีเมล</p>
                        <p class="font-semibold text-sm">{{ site.email }}</p>
                    </div>
                </a>
                <a href="/contact"
                   class="flex items-center gap-2 px-6 py-2.5 bg-red-700 hover:bg-red-800 text-white rounded-full text-sm font-semibold transition font-kanit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/>
                    </svg>
                    ปรึกษาฟรี
                </a>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             WORK PROCESS (ขั้นตอนการทำงาน)
        ══════════════════════════════════════════════════════ -->
        <section v-if="workSteps.length" class="py-16 lg:py-24 bg-gray-900">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-14">
                    <p class="text-xs font-bold tracking-widest uppercase text-red-400 mb-2">HOW WE WORK</p>
                    <h2 class="text-3xl lg:text-4xl font-bold text-white font-kanit">ขั้นตอนการทำงาน</h2>
                    <p class="mt-3 text-gray-400 font-kanit">สร้างเว็บไซต์คุณภาพได้ใน {{ workSteps.length }} ขั้นตอน ง่าย รวดเร็ว ตรงเวลา</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="(step, i) in workSteps" :key="step.id" class="relative">
                        <!-- Connector line -->
                        <div v-if="i < workSteps.length - 1"
                             class="hidden lg:block absolute top-10 left-[calc(50%+40px)] right-0 h-px bg-gradient-to-r from-red-700/60 to-transparent z-0"/>
                        <div class="relative z-10 text-center">
                            <!-- Step circle -->
                            <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-br from-red-800 to-red-600 flex items-center justify-center shadow-lg shadow-red-900/30 mb-5">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" :d="ICON_PATHS[step.icon_name] ?? ICON_PATHS.rocket"/>
                                </svg>
                            </div>
                            <!-- Number badge -->
                            <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1 ml-8 w-6 h-6 rounded-full bg-white text-gray-900 text-xs font-black flex items-center justify-center shadow">
                                {{ step.step_number }}
                            </div>
                            <h3 class="text-base font-bold text-white font-kanit mb-2">{{ step.title }}</h3>
                            <p class="text-gray-400 text-sm font-kanit leading-relaxed">{{ step.description }}</p>
                        </div>
                    </div>
                </div>
                <!-- CTA -->
                <div class="text-center mt-12">
                    <a href="/contact"
                       class="inline-flex items-center gap-2 px-8 py-3.5 bg-red-700 hover:bg-red-800 text-white font-bold rounded-full text-sm transition font-kanit shadow-lg shadow-red-900/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
                        </svg>
                        เริ่มโปรเจกต์กับเรา
                    </a>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             WHY CHOOSE US (ทำไมต้องเลือกเรา)
        ══════════════════════════════════════════════════════ -->
        <section v-if="whyUsItems.length" class="py-16 lg:py-24 bg-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-12">
                    <p class="text-xs font-bold tracking-widest uppercase text-red-700 mb-2">WHY US</p>
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 font-kanit">ทำไมต้องเลือกเรา</h2>
                    <p class="mt-3 text-gray-500 font-kanit max-w-xl mx-auto">เราไม่ได้แค่ทำเว็บ — เราเป็นพาร์ทเนอร์ที่เข้าใจธุรกิจของคุณ</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="item in whyUsItems" :key="item.id"
                         class="group flex gap-4 p-6 rounded-2xl border border-gray-100 hover:border-red-100 hover:shadow-md transition-all duration-300 hover:bg-red-50/30">
                        <div class="w-12 h-12 rounded-xl bg-red-50 group-hover:bg-red-100 flex items-center justify-center flex-shrink-0 transition">
                            <svg class="w-6 h-6 text-red-800" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="ICON_PATHS[item.icon_name] ?? ICON_PATHS.star"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 font-kanit mb-1.5">{{ item.title }}</h3>
                            <p class="text-sm text-gray-500 font-kanit leading-relaxed">{{ item.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             PARTNERS / CLIENTS
        ══════════════════════════════════════════════════════ -->
        <section v-if="partners.length"
                 class="py-14 overflow-hidden"
                 :style="`background: ${partnerSettings.bg_color ?? '#7c1d1d'}`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 mb-8">
                <h2 class="text-2xl lg:text-3xl font-bold text-white font-kanit">
                    {{ partnerSettings.heading ?? 'ลูกค้าที่ให้ความไว้วางใจกับเรา' }}
                </h2>
            </div>

            <!-- White card with marquee -->
            <div class="mx-6 lg:mx-8 bg-white rounded-2xl shadow-lg py-8 overflow-hidden">
                <div class="marquee-track" :class="{ 'pause-on-hover': true }">
                    <div class="marquee-inner">
                        <a v-for="(item, i) in marqueeItems" :key="`${item.id}-${i}`"
                           :href="item.website_url ?? undefined"
                           :target="item.website_url ? '_blank' : undefined"
                           :rel="item.website_url ? 'noopener noreferrer' : undefined"
                           class="marquee-item"
                           :class="{ 'cursor-default': !item.website_url }">
                            <div class="flex flex-col items-center gap-2 px-8">
                                <div class="h-14 flex items-center justify-center">
                                    <img v-if="item.logo_url"
                                         :src="item.logo_url"
                                         :alt="item.name"
                                         class="max-h-full max-w-[120px] object-contain transition-all duration-300"
                                         :class="partnerSettings.grayscale
                                             ? 'grayscale opacity-60 hover:grayscale-0 hover:opacity-100'
                                             : 'hover:scale-105'"/>
                                    <div v-else
                                         class="px-4 py-2 bg-gray-100 rounded-lg text-xs font-bold text-gray-400 transition hover:bg-gray-200">
                                        {{ item.name }}
                                    </div>
                                </div>
                                <span v-if="partnerSettings.show_name"
                                      class="text-xs text-gray-500 font-medium text-center whitespace-nowrap">
                                    {{ item.name }}
                                </span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             TESTIMONIALS
        ══════════════════════════════════════════════════════ -->
        <section v-if="testimonials.length"
                 class="py-16 lg:py-24 overflow-hidden"
                 :style="`background: ${testimonialSettings.bg_color ?? '#7c1d1d'}`">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

                    <!-- Left: featured image + overlay badge -->
                    <div class="relative hidden lg:block">
                        <div v-if="testimonialSettings.featured_image_url"
                             class="rounded-3xl overflow-hidden aspect-[3/4] max-w-sm mx-auto shadow-2xl">
                            <img :src="testimonialSettings.featured_image_url"
                                 alt="" class="w-full h-full object-cover"/>
                        </div>
                        <!-- Placeholder if no image -->
                        <div v-else
                             class="rounded-3xl aspect-[3/4] max-w-sm mx-auto bg-white/10 flex items-center justify-center">
                            <svg class="w-20 h-20 text-white/20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                        </div>

                        <!-- Avatar stack badge -->
                        <div v-if="avatarStack.length"
                             class="absolute bottom-6 left-1/2 -translate-x-1/2 lg:left-auto lg:translate-x-0 lg:-right-6
                                    bg-white rounded-2xl shadow-xl px-4 py-3 flex items-center gap-3 min-w-[220px]">
                            <div class="flex -space-x-2">
                                <div v-for="t in avatarStack" :key="t.id"
                                     class="w-9 h-9 rounded-full border-2 border-white overflow-hidden flex-shrink-0 flex items-center justify-center text-white text-xs font-bold"
                                     :style="t.avatar_url ? '' : `background: ${t.avatar_color}`">
                                    <img v-if="t.avatar_url" :src="t.avatar_url" class="w-full h-full object-cover"/>
                                    <span v-else>{{ initialChar(t.customer_name) }}</span>
                                </div>
                            </div>
                            <p v-if="testimonialSettings.overlay_text" class="text-xs font-semibold text-gray-700 leading-tight">
                                {{ testimonialSettings.overlay_text }}
                            </p>
                        </div>
                    </div>

                    <!-- Right: heading + carousel -->
                    <div>
                        <p class="text-xs font-bold tracking-widest uppercase text-white/60 mb-3">
                            {{ testimonialSettings.label ?? 'TESTIMONIALS' }}
                        </p>
                        <h2 class="text-4xl lg:text-5xl font-bold text-white font-kanit mb-8">
                            {{ testimonialSettings.heading ?? 'กำลังใจสำคัญของเรา' }}
                        </h2>

                        <!-- Card carousel -->
                        <div class="relative"
                             @mouseenter="stopAutoplay"
                             @mouseleave="startAutoplay">

                            <!-- Cards -->
                            <div class="overflow-hidden">
                                <Transition name="slide-card" mode="out-in">
                                    <div :key="activeSlide" class="bg-white rounded-2xl p-6 shadow-lg">
                                        <!-- Reviewer -->
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-11 h-11 rounded-full overflow-hidden flex-shrink-0 flex items-center justify-center text-white font-bold text-lg"
                                                 :style="testimonials[activeSlide]?.avatar_url ? '' : `background: ${testimonials[activeSlide]?.avatar_color}`">
                                                <img v-if="testimonials[activeSlide]?.avatar_url"
                                                     :src="testimonials[activeSlide].avatar_url"
                                                     class="w-full h-full object-cover"/>
                                                <span v-else>{{ initialChar(testimonials[activeSlide]?.customer_name) }}</span>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900 text-sm">{{ testimonials[activeSlide]?.customer_name }}</p>
                                                <p v-if="testimonials[activeSlide]?.customer_title"
                                                   class="text-xs text-gray-400">{{ testimonials[activeSlide]?.customer_title }}</p>
                                            </div>
                                            <!-- Source icon -->
                                            <div class="ml-auto">
                                                <svg v-if="testimonials[activeSlide]?.source === 'google'"
                                                     class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                                </svg>
                                                <svg v-else-if="testimonials[activeSlide]?.source === 'facebook'"
                                                     class="w-5 h-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                </svg>
                                            </div>
                                        </div>

                                        <hr class="border-gray-100 mb-4"/>

                                        <p class="text-gray-700 text-sm leading-relaxed">{{ testimonials[activeSlide]?.review_text }}</p>

                                        <!-- Stars -->
                                        <div class="flex gap-0.5 mt-4">
                                            <span v-for="s in 5" :key="s"
                                                  class="text-lg"
                                                  :class="s <= (testimonials[activeSlide]?.rating ?? 5) ? 'text-amber-400' : 'text-gray-200'">★</span>
                                        </div>
                                    </div>
                                </Transition>
                            </div>

                            <!-- Dots + arrows -->
                            <div class="flex items-center gap-4 mt-6">
                                <!-- Prev -->
                                <button @click="prevSlide"
                                        class="w-10 h-10 rounded-full border-2 border-white/40 text-white hover:border-white hover:bg-white/10 transition flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                                </button>

                                <!-- Dots -->
                                <div class="flex gap-1.5 flex-1">
                                    <button v-for="(_, i) in testimonials" :key="i"
                                            @click="goSlide(i)"
                                            class="h-1.5 rounded-full transition-all duration-300"
                                            :class="activeSlide === i ? 'bg-white w-6' : 'bg-white/30 w-1.5 hover:bg-white/60'"/>
                                </div>

                                <!-- Next -->
                                <button @click="nextSlide"
                                        class="w-10 h-10 rounded-full border-2 border-white/40 text-white hover:border-white hover:bg-white/10 transition flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                                </button>
                            </div>

                            <!-- CTA button -->
                            <div v-if="testimonialSettings.cta_text" class="mt-6">
                                <a :href="testimonialSettings.cta_url ?? '#'"
                                   :target="testimonialSettings.cta_url?.startsWith('http') ? '_blank' : undefined"
                                   class="inline-block border-2 border-white/70 text-white text-sm font-semibold px-6 py-2.5 rounded-full hover:bg-white hover:text-gray-900 transition">
                                    {{ testimonialSettings.cta_text }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             FAQ (คำถามพบบ่อย)
        ══════════════════════════════════════════════════════ -->
        <section v-if="faqs.length" class="py-16 lg:py-24 bg-gray-50">
            <div class="max-w-3xl mx-auto px-6 lg:px-8">
                <div class="text-center mb-12">
                    <p class="text-xs font-bold tracking-widest uppercase text-red-700 mb-2">FAQ</p>
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 font-kanit">คำถามพบบ่อย</h2>
                    <p class="mt-3 text-gray-500 font-kanit">หากไม่พบคำตอบ <a href="/contact" class="text-red-700 underline hover:text-red-900">ติดต่อเราได้เลย</a></p>
                </div>
                <div class="space-y-3">
                    <div v-for="faq in faqs" :key="faq.id"
                         class="bg-white rounded-xl border border-gray-200 overflow-hidden transition-all duration-200"
                         :class="openFaqId === faq.id ? 'shadow-md border-red-200' : 'hover:border-gray-300'">
                        <button @click="toggleFaq(faq.id)"
                                class="w-full flex items-center justify-between px-6 py-4 text-left gap-4">
                            <span class="font-semibold text-gray-900 font-kanit text-sm leading-snug">{{ faq.question }}</span>
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0 transition-transform duration-200"
                                 :class="openFaqId === faq.id ? 'rotate-180 text-red-700' : ''"
                                 fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/>
                            </svg>
                        </button>
                        <Transition name="faq-expand">
                            <div v-if="openFaqId === faq.id"
                                 class="px-6 pb-5 text-sm text-gray-600 font-kanit leading-relaxed border-t border-gray-100">
                                <p class="pt-4">{{ faq.answer }}</p>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════
             BLOG PREVIEW (บทความล่าสุด)
        ══════════════════════════════════════════════════════ -->
        <section v-if="latestBlogs.length" class="py-16 lg:py-24 bg-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex items-end justify-between mb-10">
                    <div>
                        <p class="text-xs font-bold tracking-widest uppercase text-red-700 mb-2">BLOG</p>
                        <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 font-kanit">บทความล่าสุด</h2>
                    </div>
                    <Link href="/blog"
                          class="hidden sm:flex items-center gap-1.5 text-sm font-semibold text-red-700 hover:text-red-900 font-kanit transition">
                        ดูทั้งหมด
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                        </svg>
                    </Link>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link v-for="blog in latestBlogs" :key="blog.id"
                          :href="route('blog.show', blog.slug)"
                          class="group block bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                        <!-- Cover -->
                        <div class="relative h-48 overflow-hidden bg-gray-100">
                            <img v-if="blog.cover_image_url" :src="blog.cover_image_url" :alt="blog.title"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"/>
                            <div v-else class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z"/>
                                </svg>
                            </div>
                        </div>
                        <!-- Body -->
                        <div class="p-5">
                            <p v-if="blog.published_at" class="text-xs text-gray-400 font-kanit mb-2">
                                {{ new Date(blog.published_at).toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                            </p>
                            <h3 class="font-bold text-gray-900 font-kanit text-sm leading-snug mb-2 line-clamp-2 group-hover:text-red-800 transition">
                                {{ blog.title }}
                            </h3>
                            <p v-if="blog.excerpt" class="text-xs text-gray-500 font-kanit leading-relaxed line-clamp-3">{{ blog.excerpt }}</p>
                            <div class="flex items-center gap-1 mt-4 text-red-700 text-xs font-semibold font-kanit">
                                อ่านต่อ
                                <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                                </svg>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

    </PublicLayout>
</template>

<!-- ── Hero text block (inline component) ────────────────────────────────── -->
<script>
import { defineComponent, h } from 'vue';
const HeroText = defineComponent({
    props: ['hero', 'animated', 'animMode', 'isDark', 'displayHighlight', 'showCursor'],
    setup(p) {
        const delay = (n) => `animation-delay: ${n}s`;
        const cls   = (n) => [
            p.animMode === 'fade'     ? 'fade-item'     : 'slide-item',
            p.animated               ? 'anim-visible'  : '',
        ];
        return () => h('div', { class: 'space-y-5' }, [
            // Badge
            p.hero?.badge_text && h('p', {
                class: [...cls(), 'text-xs font-bold tracking-[0.2em] uppercase', p.isDark ? 'text-gray-500' : 'text-white/70'].join(' '),
                style: delay(0),
            }, p.hero.badge_text),

            // Headline
            h('h1', {
                class: [...cls(), 'text-4xl md:text-5xl lg:text-6xl font-bold leading-tight'].join(' '),
                style: delay(0.1),
            }, [
                h('span', { class: p.isDark ? 'text-gray-900' : 'text-white' }, (p.hero?.headline ?? '') + ' '),
                p.displayHighlight && h('span', { class: 'text-red-500' }, [
                    p.displayHighlight,
                    p.animMode === 'typewriter' && h('span', {
                        class: ['border-r-2 border-red-500 ml-0.5', p.showCursor ? 'opacity-100' : 'opacity-0'].join(' '),
                    }, ' '),
                ]),
            ]),

            // Subtext
            p.hero?.subtext && h('p', {
                class: [...cls(), 'text-base md:text-lg max-w-xl leading-relaxed', p.isDark ? 'text-gray-600' : 'text-white/80'].join(' '),
                style: delay(0.2),
            }, p.hero.subtext),

            // CTA
            p.hero?.cta_text && h('div', { style: delay(0.3), class: cls().join(' ') }, [
                h('a', {
                    href: p.hero.cta_url ?? '#',
                    target: p.hero.cta_url?.startsWith('http') ? '_blank' : undefined,
                    class: [
                        'inline-flex items-center gap-2 px-8 py-3 rounded-full text-lg font-bold border-2 transition hover:scale-105 active:scale-95',
                        p.isDark
                            ? 'border-gray-900 text-gray-900 hover:bg-gray-900 hover:text-white'
                            : 'border-white text-white hover:bg-white hover:text-gray-900',
                    ].join(' '),
                }, p.hero.cta_text),
            ]),
        ]);
    },
});
export default { components: { HeroText } };
</script>

<style scoped>
/* ── Slide Up ── */
.slide-item {
    opacity: 0;
    transform: translateY(28px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}
.slide-item.anim-visible {
    opacity: 1;
    transform: translateY(0);
}

/* ── Fade ── */
.fade-item {
    opacity: 0;
    transition: opacity 0.8s ease;
}
.fade-item.anim-visible {
    opacity: 1;
}

/* ── Stats ── */
.stat-item {
    opacity: 0;
    transform: translateY(16px);
    transition: opacity 0.5s ease, transform 0.5s ease;
}
.stat-item.stat-visible {
    opacity: 1;
    transform: translateY(0);
}

/* ── Floating illustration ── */
.float-anim {
    animation: floatY 4s ease-in-out infinite;
}
@keyframes floatY {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-16px); }
}

/* ── Wave lines ── */
.wave-path {
    stroke: #6b7280;
    animation: waveDraw 8s ease-in-out infinite alternate;
    stroke-dasharray: 2000;
    stroke-dashoffset: 2000;
    animation: waveReveal 3s ease forwards, wavePulse 6s ease-in-out 3s infinite alternate;
}
.wave-path-2 { animation-delay: 0.4s, 3.4s; }
.wave-path-3 { animation-delay: 0.8s, 3.8s; }

@keyframes waveReveal {
    to { stroke-dashoffset: 0; }
}
@keyframes wavePulse {
    0%   { opacity: 0.15; }
    100% { opacity: 0.35; }
}

/* ── Testimonial card slide ── */
.slide-card-enter-active,
.slide-card-leave-active { transition: opacity 0.35s ease, transform 0.35s ease; }
.slide-card-enter-from   { opacity: 0; transform: translateX(24px); }
.slide-card-leave-to     { opacity: 0; transform: translateX(-24px); }

/* ── FAQ expand ── */
.faq-expand-enter-active, .faq-expand-leave-active {
    transition: max-height 0.25s ease, opacity 0.2s ease;
    max-height: 300px;
    overflow: hidden;
}
.faq-expand-enter-from, .faq-expand-leave-to {
    max-height: 0;
    opacity: 0;
}

/* ── Partner marquee ── */
.marquee-track {
    overflow: hidden;
    width: 100%;
}
.marquee-track:hover .marquee-inner {
    animation-play-state: paused;
}
.marquee-inner {
    display: flex;
    width: max-content;
    animation: marqueeScroll 30s linear infinite;
}
.marquee-item {
    display: flex;
    align-items: center;
    flex-shrink: 0;
    text-decoration: none;
}
@keyframes marqueeScroll {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
</style>
