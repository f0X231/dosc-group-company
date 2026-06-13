<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    hero:                { type: Object, default: null },
    testimonialSettings: { type: Object, default: () => ({}) },
    testimonials:        { type: Array,  default: () => [] },
    partnerSettings:     { type: Object, default: () => ({}) },
    partners:            { type: Array,  default: () => [] },
});

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
// Duplicate list so the seamless loop never shows a gap
const marqueeItems = computed(() => {
    if (props.partners.length === 0) return [];
    // Need at least enough copies to fill 2× viewport
    const copies = Math.max(2, Math.ceil(12 / props.partners.length) + 1);
    return Array.from({ length: copies }, () => props.partners).flat();
});
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
             REMAINING SECTIONS (placeholders)
        ══════════════════════════════════════════════════════ -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 text-center font-kanit">
                <h2 class="text-2xl font-bold text-gray-900">แพ็กเกจของเรา</h2>
                <p class="mt-2 text-gray-400 text-sm">placeholder</p>
            </div>
        </section>

        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 text-center font-kanit">
                <h2 class="text-2xl font-bold text-gray-900">ทำไมต้องเลือกเรา</h2>
                <p class="mt-2 text-gray-400 text-sm">placeholder</p>
            </div>
        </section>

        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 text-center font-kanit">
                <h2 class="text-2xl font-bold text-gray-900">ขั้นตอนการทำงาน</h2>
                <p class="mt-2 text-gray-400 text-sm">placeholder</p>
            </div>
        </section>

        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 text-center font-kanit">
                <h2 class="text-2xl font-bold text-gray-900">ผลงานที่ผ่านมา</h2>
                <p class="mt-2 text-gray-400 text-sm">placeholder</p>
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
