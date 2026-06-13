<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    hero: { type: Object, default: null },
});

// ─── Animation ────────────────────────────────────────────────────────────────
const animated       = ref(false);
const typedText      = ref('');
const showCursor     = ref(true);

onMounted(() => {
    // Trigger slide_up / fade after small delay
    requestAnimationFrame(() => { animated.value = true; });

    if (props.hero?.animation_style === 'typewriter' && props.hero?.headline_highlight) {
        runTypewriter(props.hero.headline_highlight);
    }
});

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

        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 text-center font-kanit">
                <h2 class="text-2xl font-bold text-gray-900">รีวิวจากลูกค้า</h2>
                <p class="mt-2 text-gray-400 text-sm">placeholder</p>
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
</style>
