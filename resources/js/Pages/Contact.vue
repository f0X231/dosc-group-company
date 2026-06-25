<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const page = usePage();
const success = computed(() => page.props.flash?.success);

const form = useForm({
    name: '',
    email: '',
    phone: '',
    message: '',
});

function submit() {
    form.post(route('contact.store'), {
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <PublicLayout>

        <!-- Page Header -->
        <section class="bg-gray-50 py-14 text-center border-b border-gray-200">
            <div class="max-w-3xl mx-auto px-4">
                <h1 class="text-3xl lg:text-4xl xl:text-5xl font-bold text-gray-900 font-kanit">ติดต่อเรา</h1>
                <p class="mt-3 text-gray-500 text-base">มีข้อสงสัยหรืออยากปรึกษาเรื่องเว็บไซต์? ส่งข้อความมาได้เลย</p>
            </div>
        </section>

        <!-- Content -->
        <section class="py-16 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-2 gap-12 lg:gap-20 items-start">

                    <!-- Left: Contact Info -->
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">ช่องทางติดต่อ</h2>
                        <p class="text-gray-500 text-sm mb-8">เราพร้อมให้คำปรึกษาและตอบทุกคำถาม</p>

                        <ul class="space-y-5">
                            <li class="flex items-start gap-4">
                                <span class="w-10 h-10 flex-shrink-0 flex items-center justify-center rounded-full bg-green-50 text-green-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.338c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 4c.828 0 1.655.063 2.465.188C9.65 4.371 10.5 5.4 10.5 6.584v3.33c0 1.007-.603 1.927-1.548 2.308L7.3 12.92c.618 1.3 1.556 2.455 2.72 3.32l.703-1.65c.38-.946 1.3-1.549 2.308-1.549h3.33c1.184 0 2.213.85 2.396 2.035.125.81.188 1.637.188 2.465 0 .569-.044 1.127-.132 1.668-.09 1.174-1.076 2.085-2.254 2.085C9.11 22.5 1.5 14.89 1.5 5.5c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 3"/></svg>
                                </span>
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-0.5">โทรศัพท์</p>
                                    <a href="tel:081-689-9976" class="text-gray-800 font-medium hover:text-green-600 transition">081-689-9976</a>
                                </div>
                            </li>
                            <li class="flex items-start gap-4">
                                <span class="w-10 h-10 flex-shrink-0 flex items-center justify-center rounded-full bg-green-50 text-green-500">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="m12 .5c-6.615 0-12 4.398-12 9.803 0 4.841 4.27 8.897 10.035 9.608.391.084.922.258 1.057.593.12.301.079.771.038 1.073l-.164 1.013c-.045.301-.24 1.186 1.049.647 1.291-.539 6.916-4.103 9.436-7.023 1.724-1.952 2.549-3.965 2.549-6.311 0-5.405-5.385-9.803-12-9.803z"/></svg>
                                </span>
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-0.5">LINE</p>
                                    <a href="https://line.me/ti/p/@dosc" target="_blank" rel="noopener" class="text-gray-800 font-medium hover:text-green-600 transition">@dosc</a>
                                </div>
                            </li>
                            <li class="flex items-start gap-4">
                                <span class="w-10 h-10 flex-shrink-0 flex items-center justify-center rounded-full bg-green-50 text-green-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                </span>
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-0.5">อีเมล</p>
                                    <a href="mailto:info@dosc.co.th" class="text-gray-800 font-medium hover:text-green-600 transition">info@dosc.co.th</a>
                                </div>
                            </li>
                            <li class="flex items-start gap-4">
                                <span class="w-10 h-10 flex-shrink-0 flex items-center justify-center rounded-full bg-green-50 text-green-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                </span>
                                <div>
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-0.5">ที่อยู่</p>
                                    <p class="text-gray-800 font-medium leading-relaxed">กรุงเทพมหานคร ประเทศไทย</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Right: Form -->
                    <div>
                        <!-- Success Message -->
                        <div v-if="success"
                             class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-4 py-3.5 rounded-xl text-sm font-medium">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            {{ success }}
                        </div>

                        <h2 class="text-2xl font-bold text-gray-900 mb-2">ส่งข้อความหาเรา</h2>
                        <p class="text-gray-500 text-sm mb-8">กรอกข้อมูลด้านล่าง เราจะติดต่อกลับภายใน 24 ชั่วโมง</p>

                        <form @submit.prevent="submit" class="space-y-4">

                            <!-- Name -->
                            <div>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="ชื่อ - นามสกุล *"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
                                />
                                <p v-if="form.errors.name" class="text-red-500 text-xs mt-1.5">{{ form.errors.name }}</p>
                            </div>

                            <!-- Email -->
                            <div>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    placeholder="อีเมล *"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
                                />
                                <p v-if="form.errors.email" class="text-red-500 text-xs mt-1.5">{{ form.errors.email }}</p>
                            </div>

                            <!-- Phone -->
                            <div>
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    placeholder="เบอร์โทรศัพท์ (ไม่บังคับ)"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition"
                                />
                            </div>

                            <!-- Message -->
                            <div>
                                <textarea
                                    v-model="form.message"
                                    rows="5"
                                    placeholder="ข้อความ *"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition resize-none"
                                ></textarea>
                                <p v-if="form.errors.message" class="text-red-500 text-xs mt-1.5">{{ form.errors.message }}</p>
                            </div>

                            <!-- Submit -->
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-green-500 hover:bg-green-600 disabled:opacity-60 text-white font-semibold py-3.5 rounded-xl transition shadow-sm text-sm"
                            >
                                {{ form.processing ? 'กำลังส่ง...' : 'ส่งข้อความ' }}
                            </button>

                        </form>
                    </div>

                </div>
            </div>
        </section>

    </PublicLayout>
</template>
