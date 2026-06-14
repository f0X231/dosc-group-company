<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const page = usePage();
const site = computed(() => page.props.site ?? {});

const sections = [
    {
        id: 1,
        title: '1. ความหมายของข้อมูลส่วนบุคคล',
        content: `ข้อมูลส่วนบุคคล หมายถึง ข้อมูลเกี่ยวกับบุคคลซึ่งทำให้สามารถระบุตัวบุคคลนั้นได้ไม่ว่าทางตรงหรือทางอ้อม แต่ไม่รวมถึงข้อมูลของผู้ถึงแก่กรรมโดยเฉพาะ ทั้งนี้ตามที่กฎหมายที่เกี่ยวข้องกำหนด`,
    },
    {
        id: 2,
        title: '2. การเก็บรวบรวมข้อมูลส่วนบุคคล',
        subsections: [
            {
                label: '2.1 ประเภทของข้อมูลส่วนบุคคลที่เก็บรวบรวม',
                items: [
                    'ชื่อ-นามสกุล ที่อยู่ อีเมล หมายเลขโทรศัพท์',
                    'ข้อมูล Social Media เช่น LINE ID, Facebook',
                    'ข้อมูลอื่น ๆ ที่ท่านให้ไว้โดยสมัครใจ',
                ],
            },
            {
                label: '2.2 วิธีการเก็บรวบรวมข้อมูล',
                text: 'บริษัทเก็บรวบรวมข้อมูลผ่านการติดต่อโดยตรง เว็บไซต์ของบริษัท หรือจากแหล่งข้อมูลอื่น เช่น องค์กร ผู้ให้บริการด้านความปลอดภัย และแพลตฟอร์ม Social Media',
            },
            {
                label: '2.3 ระยะเวลาการเก็บรักษาข้อมูล',
                text: 'บริษัทจะเก็บรักษาข้อมูลส่วนบุคคลของท่านตลอดระยะเวลาที่ท่านเป็นลูกค้า และต่อเนื่องอีก 10 ปีหลังจากนั้น หรือตามที่กฎหมายกำหนด',
            },
        ],
    },
    {
        id: 3,
        title: '3. วัตถุประสงค์ในการเก็บรวบรวมข้อมูล',
        items: [
            'เพื่อการทำการตลาดและส่งเสริมการขาย',
            'เพื่อให้ข้อมูลและแนะนำสินค้า/บริการ',
            'เพื่อพัฒนาและปรับปรุงบริการ',
            'เพื่อการติดต่อสื่อสารและบริการลูกค้า',
            'เพื่อยืนยันตัวตนของท่าน',
            'เพื่อการวิจัยและพัฒนา',
            'เพื่อการส่งมอบบริการและการสื่อสาร',
            'เพื่อปฏิบัติตามสัญญา',
            'เพื่อการปฏิบัติตามกฎหมายและประโยชน์สาธารณะ',
            'เพื่อปกป้องสุขภาพและความปลอดภัย',
        ],
    },
    {
        id: 4,
        title: '4. การคุ้มครองข้อมูลส่วนบุคคล',
        content: `บริษัทได้นำมาตรการรักษาความปลอดภัยทั้งทางเทคนิคและทางบริหารมาใช้เพื่อปกป้องข้อมูลส่วนบุคคลของท่านจากการเข้าถึง ใช้ เปิดเผย แก้ไข หรือทำลายโดยไม่ได้รับอนุญาต พนักงานของบริษัทมีหน้าที่รักษาความลับของข้อมูล กรณีที่เจ้าของข้อมูลเป็นผู้เยาว์หรือผู้ไร้ความสามารถ จำเป็นต้องได้รับความยินยอมจากผู้ปกครองหรือผู้แทนโดยชอบธรรม`,
    },
    {
        id: 5,
        title: '5. การเปิดเผยข้อมูลส่วนบุคคล',
        subsections: [
            {
                label: '5.1 ผู้รับข้อมูล',
                text: 'บริษัทอาจเปิดเผยข้อมูลให้แก่ผู้ให้บริการและพันธมิตรทางธุรกิจ เช่น ผู้ให้บริการวิเคราะห์ข้อมูล ผู้ให้บริการจัดส่ง ผู้ให้บริการด้านความปลอดภัย ผู้พัฒนาแอปพลิเคชัน และผู้ให้บริการด้านการสื่อสาร',
            },
            {
                label: '5.2 การโอนข้อมูลระหว่างประเทศ',
                text: 'ข้อมูลส่วนบุคคลของท่านอาจถูกโอนไปยังเซิร์ฟเวอร์ต่างประเทศที่มีมาตรฐานการคุ้มครองข้อมูลเพียงพอตามกฎหมายไทย',
            },
        ],
    },
    {
        id: 6,
        title: '6. สิทธิของเจ้าของข้อมูลส่วนบุคคล',
        items: [
            'สิทธิในการถอนความยินยอม',
            'สิทธิในการเข้าถึง คัดลอก แก้ไข หรืออัปเดตข้อมูล',
            'สิทธิในการทราบแหล่งที่มาของข้อมูล',
            'สิทธิในการขอรับข้อมูลในรูปแบบที่สามารถถ่ายโอนได้',
            'สิทธิในการคัดค้านการประมวลผลข้อมูล',
            'สิทธิในการขอลบข้อมูลในกรณีที่เป็นไปตามเงื่อนไข',
            'สิทธิในการร้องเรียนต่อหน่วยงานที่มีอำนาจ',
        ],
    },
    {
        id: 7,
        title: '7. การใช้คุกกี้ (Cookies)',
        subsections: [
            {
                label: 'ประเภทของคุกกี้ที่ใช้',
                items: [
                    'Functional Cookies — สำหรับเข้าถึงบัญชีและการตั้งค่าผู้ใช้',
                    'Advertising Cookies — ติดตามความสนใจและประเมินแคมเปญโฆษณา',
                    'Technical Cookies — สำหรับการดำเนินงานพื้นฐานของเว็บไซต์',
                    'Performance Cookies — วิเคราะห์ข้อมูลผู้เยี่ยมชมแบบไม่ระบุตัวตน',
                    'Third-party Cookies — คุกกี้จากบุคคลที่สาม',
                ],
            },
            {
                label: 'วัตถุประสงค์การใช้คุกกี้',
                text: 'เพื่อเพิ่มประสิทธิภาพแพลตฟอร์ม นำเสนอเนื้อหาที่ตรงความสนใจ ปรับปรุงความปลอดภัย และวิเคราะห์พฤติกรรมการใช้งาน',
            },
        ],
    },
    {
        id: 8,
        title: '8. การจัดการคุกกี้',
        content: `ท่านสามารถตั้งค่าเบราว์เซอร์เพื่อปฏิเสธคุกกี้ได้ อย่างไรก็ตาม การปฏิเสธคุกกี้อาจส่งผลต่อการใช้งานบางส่วนของเว็บไซต์`,
    },
    {
        id: 9,
        title: '9. การแก้ไขนโยบาย',
        content: `บริษัทขอสงวนสิทธิ์ในการแก้ไขนโยบายนี้ โดยจะแจ้งให้ท่านทราบ และนโยบายที่แก้ไขจะมีผลทันทีเว้นแต่กฎหมายกำหนดให้ต้องได้รับความยินยอม`,
    },
];
</script>

<template>
    <Head title="นโยบายความเป็นส่วนตัว | DOSC Group"/>
    <PublicLayout>

        <!-- Page Header -->
        <section class="bg-gradient-to-br from-gray-900 to-gray-800 py-14">
            <div class="max-w-4xl mx-auto px-6 lg:px-8">
                <nav class="flex items-center gap-2 text-xs text-gray-400 font-kanit mb-5">
                    <Link href="/" class="hover:text-gray-200 transition">หน้าแรก</Link>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                    </svg>
                    <span class="text-gray-300">นโยบายความเป็นส่วนตัว</span>
                </nav>
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-700/30 flex items-center justify-center flex-shrink-0 mt-1">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-white font-kanit">นโยบายความเป็นส่วนตัว</h1>
                        <p class="text-gray-400 font-kanit text-sm mt-1">Privacy Policy — {{ site.site_name ?? 'DOSC Group' }}</p>
                        <p class="text-gray-500 font-kanit text-xs mt-2">มีผลบังคับใช้ตั้งแต่วันที่ 1 มกราคม 2567</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Content -->
        <section class="py-12 bg-white">
            <div class="max-w-4xl mx-auto px-6 lg:px-8">
                <div class="grid lg:grid-cols-4 gap-10">

                    <!-- Sticky TOC sidebar -->
                    <aside class="hidden lg:block">
                        <div class="sticky top-24">
                            <p class="text-xs font-bold tracking-widest uppercase text-gray-400 mb-3 font-kanit">สารบัญ</p>
                            <nav class="space-y-1">
                                <a v-for="s in sections" :key="s.id"
                                   :href="`#section-${s.id}`"
                                   class="block text-xs text-gray-500 hover:text-red-700 hover:pl-2 transition-all font-kanit py-0.5 border-l-2 border-transparent hover:border-red-600 pl-2">
                                    {{ s.id }}. {{ s.title.split('. ').slice(1).join('. ') }}
                                </a>
                                <a href="#section-contact"
                                   class="block text-xs text-gray-500 hover:text-red-700 hover:pl-2 transition-all font-kanit py-0.5 border-l-2 border-transparent hover:border-red-600 pl-2">
                                    10. ข้อมูลการติดต่อ
                                </a>
                            </nav>
                        </div>
                    </aside>

                    <!-- Main content -->
                    <div class="lg:col-span-3 space-y-10">

                        <!-- Intro -->
                        <div class="bg-red-50 border border-red-100 rounded-2xl p-6 font-kanit">
                            <p class="text-sm text-gray-700 leading-relaxed">
                                บริษัท DOSC Group ให้ความสำคัญกับการคุ้มครองข้อมูลส่วนบุคคลของท่าน นโยบายความเป็นส่วนตัวนี้จัดทำขึ้นตาม
                                <strong class="text-gray-900">พระราชบัญญัติคุ้มครองข้อมูลส่วนบุคคล (PDPA) พ.ศ. 2562</strong>
                                เพื่อให้ท่านมั่นใจว่าข้อมูลของท่านจะได้รับการจัดการอย่างปลอดภัยและโปร่งใส
                            </p>
                        </div>

                        <!-- Sections -->
                        <div v-for="s in sections" :key="s.id" :id="`section-${s.id}`" class="scroll-mt-28">
                            <h2 class="text-base font-bold text-gray-900 font-kanit mb-4 flex items-center gap-2">
                                <span class="w-7 h-7 rounded-lg bg-red-700 text-white text-xs font-black flex items-center justify-center flex-shrink-0">
                                    {{ s.id }}
                                </span>
                                {{ s.title.split('. ').slice(1).join('. ') }}
                            </h2>

                            <!-- Simple paragraph -->
                            <p v-if="s.content" class="text-sm text-gray-600 font-kanit leading-relaxed">
                                {{ s.content }}
                            </p>

                            <!-- Top-level list -->
                            <ul v-if="s.items" class="space-y-2 mt-1">
                                <li v-for="item in s.items" :key="item"
                                    class="flex items-start gap-2.5 text-sm text-gray-600 font-kanit">
                                    <svg class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                    </svg>
                                    {{ item }}
                                </li>
                            </ul>

                            <!-- Subsections -->
                            <div v-if="s.subsections" class="space-y-5 mt-1">
                                <div v-for="sub in s.subsections" :key="sub.label">
                                    <p class="text-sm font-semibold text-gray-800 font-kanit mb-2">{{ sub.label }}</p>
                                    <p v-if="sub.text" class="text-sm text-gray-600 font-kanit leading-relaxed">{{ sub.text }}</p>
                                    <ul v-if="sub.items" class="space-y-2 mt-1">
                                        <li v-for="item in sub.items" :key="item"
                                            class="flex items-start gap-2.5 text-sm text-gray-600 font-kanit">
                                            <svg class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                            </svg>
                                            {{ item }}
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <hr class="mt-8 border-gray-100"/>
                        </div>

                        <!-- Section 10: Contact -->
                        <div id="section-contact" class="scroll-mt-28">
                            <h2 class="text-base font-bold text-gray-900 font-kanit mb-4 flex items-center gap-2">
                                <span class="w-7 h-7 rounded-lg bg-red-700 text-white text-xs font-black flex items-center justify-center flex-shrink-0">
                                    10
                                </span>
                                ข้อมูลการติดต่อ
                            </h2>
                            <p class="text-sm text-gray-600 font-kanit mb-5">
                                หากท่านมีคำถามเกี่ยวกับนโยบายความเป็นส่วนตัว หรือต้องการใช้สิทธิของท่าน กรุณาติดต่อเราผ่านช่องทางดังนี้
                            </p>
                            <div class="bg-gray-50 rounded-2xl p-6 space-y-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-200 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 font-kanit">ที่อยู่</p>
                                        <p class="text-sm text-gray-800 font-kanit mt-0.5">
                                            {{ site.address ?? 'กรุงเทพมหานคร ประเทศไทย' }}
                                        </p>
                                    </div>
                                </div>

                                <div v-if="site.phone" class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-200 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.338c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 4c.828 0 1.655.063 2.465.188C9.65 4.371 10.5 5.4 10.5 6.584v3.33c0 1.007-.603 1.927-1.548 2.308L7.3 12.92c.618 1.3 1.556 2.455 2.72 3.32l.703-1.65c.38-.946 1.3-1.549 2.308-1.549h3.33c1.184 0 2.213.85 2.396 2.035.125.81.188 1.637.188 2.465 0 .569-.044 1.127-.132 1.668-.09 1.174-1.076 2.085-2.254 2.085C9.11 22.5 1.5 14.89 1.5 5.5c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 3"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 font-kanit">โทรศัพท์</p>
                                        <a :href="`tel:${site.phone}`" class="text-sm text-red-700 font-bold font-kanit hover:underline">
                                            {{ site.phone }}
                                        </a>
                                    </div>
                                </div>

                                <div v-if="site.email" class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-200 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 font-kanit">อีเมล</p>
                                        <a :href="`mailto:${site.email}`" class="text-sm text-red-700 font-bold font-kanit hover:underline">
                                            {{ site.email }}
                                        </a>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-green-600" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="m12 .5c-6.615 0-12 4.398-12 9.803 0 4.841 4.27 8.897 10.035 9.608.391.084.922.258 1.057.593.12.301.079.771.038 1.073l-.164 1.013c-.045.301-.24 1.186 1.049.647 1.291-.539 6.916-4.103 9.436-7.023 1.724-1.952 2.549-3.965 2.549-6.311 0-5.405-5.385-9.803-12-9.803z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 font-kanit">LINE</p>
                                        <a href="https://line.me/ti/p/@dosc" target="_blank" rel="noopener"
                                           class="text-sm text-green-600 font-bold font-kanit hover:underline">
                                            @doscgroup
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Last updated note -->
                        <div class="text-center pt-4 pb-2">
                            <p class="text-xs text-gray-400 font-kanit">
                                นโยบายนี้อาจมีการปรับปรุงแก้ไขโดยไม่ต้องแจ้งให้ทราบล่วงหน้า<br/>
                                อัปเดตล่าสุด: มกราคม 2568
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </section>

    </PublicLayout>
</template>
