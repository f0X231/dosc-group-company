<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    portfolios: { type: Array, default: () => [] },
    tags:       { type: Array, default: () => [] },
});

const activeTag = ref('ทั้งหมด');

const filtered = computed(() => {
    if (activeTag.value === 'ทั้งหมด') return props.portfolios;
    return props.portfolios.filter(p => p.package_name === activeTag.value);
});

function onMouseEnter(e) {
    const video = e.currentTarget.querySelector('video');
    if (video) video.play().catch(() => {});
}

function onMouseLeave(e) {
    const video = e.currentTarget.querySelector('video');
    if (video) { video.pause(); video.currentTime = 0; }
}
</script>

<template>
    <Head title="ผลงานของเรา" />
    <PublicLayout>

        <!-- Hero -->
        <section class="bg-gray-50 py-16 text-center font-kanit">
            <p class="text-sm font-medium text-green-600 tracking-widest uppercase mb-3">Portfolio</p>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900">ผลงานของเรา</h1>
            <p class="mt-3 text-gray-500 max-w-xl mx-auto text-sm md:text-base">
                ผลงานเว็บไซต์ที่เราออกแบบและพัฒนาให้กับลูกค้า ครอบคลุมหลากหลายธุรกิจ
            </p>
        </section>

        <!-- Filter Tabs -->
        <section v-if="tags.length > 0" class="py-6 bg-white border-b sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 flex gap-2 justify-center flex-wrap font-kanit">
                <button
                    @click="activeTag = 'ทั้งหมด'"
                    class="px-4 py-1.5 rounded-full text-sm font-medium transition"
                    :class="activeTag === 'ทั้งหมด'
                        ? 'bg-gray-900 text-white'
                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                    ทั้งหมด
                    <span class="ml-1 text-xs opacity-70">({{ portfolios.length }})</span>
                </button>
                <button
                    v-for="tag in tags"
                    :key="tag"
                    @click="activeTag = tag"
                    class="px-4 py-1.5 rounded-full text-sm font-medium transition"
                    :class="activeTag === tag
                        ? 'bg-gray-900 text-white'
                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                    {{ tag }}
                </button>
            </div>
        </section>

        <!-- Portfolio Grid -->
        <section class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4">

                <!-- Empty state -->
                <div v-if="filtered.length === 0" class="text-center py-20 text-gray-400 font-kanit">
                    <svg class="mx-auto w-12 h-12 mb-4 opacity-40" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z"/>
                    </svg>
                    <p>ยังไม่มีผลงานในหมวดนี้</p>
                </div>

                <!-- Grid (vertical 9:16 cards) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                    <div
                        v-for="item in filtered"
                        :key="item.id"
                        class="group relative"
                        @mouseenter="onMouseEnter"
                        @mouseleave="onMouseLeave"
                    >
                        <component
                            :is="item.client_url ? 'a' : 'div'"
                            :href="item.client_url || undefined"
                            :target="item.client_url ? '_blank' : undefined"
                            :rel="item.client_url ? 'noopener noreferrer' : undefined"
                            class="block relative rounded-2xl overflow-hidden bg-gray-900"
                            style="aspect-ratio: 9/16;"
                        >
                            <video
                                v-if="item.video_url"
                                :src="item.video_url"
                                :poster="item.thumbnail_url || undefined"
                                class="w-full h-full object-cover"
                                muted
                                loop
                                playsinline
                                preload="none"
                            />

                            <!-- Overlay gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent
                                        opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                            <!-- Info on hover -->
                            <div class="absolute bottom-0 left-0 right-0 p-3 translate-y-2 group-hover:translate-y-0
                                        opacity-0 group-hover:opacity-100 transition-all duration-300 font-kanit">
                                <p class="text-white text-xs font-semibold leading-snug line-clamp-2">{{ item.title }}</p>
                                <span v-if="item.package_name"
                                      class="inline-block mt-1 text-[10px] bg-white/20 text-white px-2 py-0.5 rounded-full">
                                    {{ item.package_name }}
                                </span>
                            </div>

                            <!-- External link icon -->
                            <div v-if="item.client_url"
                                 class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="bg-white/20 backdrop-blur-sm rounded-full p-1">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                    </svg>
                                </div>
                            </div>
                        </component>

                        <!-- Title below card -->
                        <div class="mt-2 px-1 font-kanit">
                            <p class="text-xs font-medium text-gray-800 truncate">{{ item.title }}</p>
                            <p v-if="item.package_name" class="text-[10px] text-gray-400">{{ item.package_name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-16 bg-gray-50 text-center font-kanit">
            <h2 class="text-2xl font-bold text-gray-900 mb-3">อยากได้เว็บไซต์แบบนี้?</h2>
            <p class="text-gray-500 mb-6">ปรึกษาฟรี ไม่มีค่าใช้จ่าย</p>
            <a href="https://line.me/ti/p/~@dosc"
               target="_blank"
               class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold px-6 py-3 rounded-full transition">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.627-.63h2.386c.349 0 .63.285.63.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.627-.63.349 0 .631.285.631.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.281.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/>
                </svg>
                ปรึกษาฟรี คลิกเลย
            </a>
        </section>

    </PublicLayout>
</template>
