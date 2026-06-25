<script setup>
import { ref, computed } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    faqs: { type: Array, default: () => [] },
});

const openId = ref(null);

function toggle(id) {
    openId.value = openId.value === id ? null : id;
}

const leftFaqs  = computed(() => props.faqs.slice(0, Math.ceil(props.faqs.length / 2)));
const rightFaqs = computed(() => props.faqs.slice(Math.ceil(props.faqs.length / 2)));
</script>

<template>
    <PublicLayout>

        <!-- Page Header -->
        <section class="bg-gray-50 py-14 text-center border-b border-gray-200">
            <div class="max-w-3xl mx-auto px-4">
                <h1 class="text-3xl lg:text-4xl xl:text-5xl font-bold text-gray-900 font-kanit">คำถามที่พบบ่อย</h1>
                <p class="mt-3 text-gray-500 text-base">รวบรวมคำถามที่ลูกค้าสอบถามบ่อย หากมีข้อสงสัยเพิ่มเติมสามารถติดต่อเราได้เลย</p>
            </div>
        </section>

        <!-- Accordion -->
        <section class="py-14 bg-white">
            <div class="max-w-7xl mx-auto px-4">

                <!-- Empty state -->
                <div v-if="faqs.length === 0" class="text-center py-16 text-gray-400">
                    ยังไม่มีคำถามที่พบบ่อยในขณะนี้
                </div>

                <!-- 2-column grid on desktop -->
                <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-x-6 lg:gap-y-3 items-start">

                    <!-- Left column -->
                    <div class="space-y-3">
                        <div
                            v-for="faq in leftFaqs"
                            :key="faq.id"
                            class="border border-gray-200 rounded-xl overflow-hidden transition-shadow"
                            :class="openId === faq.id ? 'shadow-md' : 'hover:shadow-sm'"
                        >
                            <button
                                type="button"
                                class="w-full flex items-center justify-between gap-4 px-6 py-5 text-left bg-white hover:bg-gray-50 transition-colors"
                                :class="{ 'bg-gray-50': openId === faq.id }"
                                @click="toggle(faq.id)"
                                :aria-expanded="openId === faq.id"
                            >
                                <span class="font-semibold text-gray-900 text-base leading-snug">
                                    {{ faq.question }}
                                </span>
                                <span class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-full border-2 border-gray-300 text-gray-500 transition-all"
                                      :class="openId === faq.id ? 'border-green-500 text-green-500 bg-green-50' : ''">
                                    <svg v-if="openId !== faq.id" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                    </svg>
                                    <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/>
                                    </svg>
                                </span>
                            </button>
                            <div
                                v-show="openId === faq.id"
                                class="px-6 pb-5 pt-1 border-t border-gray-100 bg-white"
                            >
                                <p class="text-gray-600 leading-relaxed whitespace-pre-line">{{ faq.answer }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right column -->
                    <div class="space-y-3">
                        <div
                            v-for="faq in rightFaqs"
                            :key="faq.id"
                            class="border border-gray-200 rounded-xl overflow-hidden transition-shadow"
                            :class="openId === faq.id ? 'shadow-md' : 'hover:shadow-sm'"
                        >
                            <button
                                type="button"
                                class="w-full flex items-center justify-between gap-4 px-6 py-5 text-left bg-white hover:bg-gray-50 transition-colors"
                                :class="{ 'bg-gray-50': openId === faq.id }"
                                @click="toggle(faq.id)"
                                :aria-expanded="openId === faq.id"
                            >
                                <span class="font-semibold text-gray-900 text-base leading-snug">
                                    {{ faq.question }}
                                </span>
                                <span class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-full border-2 border-gray-300 text-gray-500 transition-all"
                                      :class="openId === faq.id ? 'border-green-500 text-green-500 bg-green-50' : ''">
                                    <svg v-if="openId !== faq.id" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                    </svg>
                                    <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/>
                                    </svg>
                                </span>
                            </button>
                            <div
                                v-show="openId === faq.id"
                                class="px-6 pb-5 pt-1 border-t border-gray-100 bg-white"
                            >
                                <p class="text-gray-600 leading-relaxed whitespace-pre-line">{{ faq.answer }}</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="bg-gray-50 border-t border-gray-200 py-14 text-center">
            <div class="max-w-xl mx-auto px-4">
                <h2 class="text-xl font-bold text-gray-900">ยังมีข้อสงสัยอีกไหม?</h2>
                <p class="mt-2 text-gray-500 text-sm">ทีมงานของเราพร้อมตอบทุกคำถาม ติดต่อเราได้เลย</p>
                <a href="/contact"
                   class="mt-6 inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-3 rounded-full transition shadow-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="m12 .5c-6.615 0-12 4.398-12 9.803 0 4.841 4.27 8.897 10.035 9.608.391.084.922.258 1.057.593.12.301.079.771.038 1.073l-.164 1.013c-.045.301-.24 1.186 1.049.647 1.291-.539 6.916-4.103 9.436-7.023 1.724-1.952 2.549-3.965 2.549-6.311 0-5.405-5.385-9.803-12-9.803z"/>
                    </svg>
                    ปรึกษาฟรี คลิกเลย
                </a>
            </div>
        </section>

    </PublicLayout>
</template>
