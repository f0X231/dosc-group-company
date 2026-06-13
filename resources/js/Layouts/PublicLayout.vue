<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import SocialIcon from '@/Components/SocialIcon.vue';

const mobileOpen = ref(false);
const portfolioOpen = ref(false);
const servicesOpen = ref(false);
const mobilePortfolioOpen = ref(false);
const mobileServicesOpen = ref(false);

function closeMobile() {
    mobileOpen.value = false;
    mobilePortfolioOpen.value = false;
    mobileServicesOpen.value = false;
}

const page        = usePage();
const site        = computed(() => page.props.site        ?? {});
const socialLinks = computed(() => page.props.socialLinks ?? []);
</script>

<template>
    <div class="min-h-screen flex flex-col font-kanit">

        <!-- ── Navbar ───────────────────────────────────────────────────────── -->
        <header class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">

                    <!-- Logo -->
                    <Link href="/" class="flex-shrink-0">
                        <img v-if="site.logo_url" :src="site.logo_url" :alt="site.site_name" class="h-9 w-auto object-contain"/>
                        <span v-else class="text-xl font-bold text-gray-900 tracking-tight">{{ site.site_name ?? 'DOSC Group' }}</span>
                    </Link>

                    <!-- Desktop Nav -->
                    <nav class="hidden lg:flex items-center gap-1">

                        <!-- ผลงาน dropdown -->
                        <div class="relative"
                             @mouseenter="portfolioOpen = true"
                             @mouseleave="portfolioOpen = false">
                            <button class="flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 rounded-md hover:bg-gray-50 transition">
                                ผลงาน
                                <svg class="w-3.5 h-3.5 mt-0.5 transition-transform" :class="{ 'rotate-180': portfolioOpen }" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/></svg>
                            </button>
                            <div v-show="portfolioOpen"
                                 class="absolute top-full left-0 mt-1 w-52 bg-white rounded-lg shadow-lg ring-1 ring-black/5 py-1 z-50">
                                <Link href="/portfolio" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900">ผลงานทั้งหมด</Link>
                                <div class="border-t border-gray-100 my-1"></div>
                                <Link href="/portfolio?package=one-page" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900">One Page Package</Link>
                                <Link href="/portfolio?package=starter" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900">Starter Package</Link>
                                <Link href="/portfolio?package=business" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900">Business Package</Link>
                                <Link href="/portfolio?package=e-commerce" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900">E-Commerce Package</Link>
                            </div>
                        </div>

                        <!-- บริการและราคา dropdown -->
                        <div class="relative"
                             @mouseenter="servicesOpen = true"
                             @mouseleave="servicesOpen = false">
                            <button class="flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 rounded-md hover:bg-gray-50 transition">
                                บริการและราคา
                                <svg class="w-3.5 h-3.5 mt-0.5 transition-transform" :class="{ 'rotate-180': servicesOpen }" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/></svg>
                            </button>
                            <div v-show="servicesOpen"
                                 class="absolute top-full left-0 mt-1 w-48 bg-white rounded-lg shadow-lg ring-1 ring-black/5 py-1 z-50">
                                <Link href="/services/website" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900">รับทำเว็บไซต์</Link>
                                <Link href="/services/google-ads" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900">รับทำ Google Ads</Link>
                            </div>
                        </div>

                        <Link href="/faq" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 rounded-md hover:bg-gray-50 transition">
                            คำถามที่พบบ่อย
                        </Link>
                        <Link href="/contact" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 rounded-md hover:bg-gray-50 transition">
                            ติดต่อเรา
                        </Link>
                    </nav>

                    <!-- CTA + Mobile Toggle -->
                    <div class="flex items-center gap-3">
                        <a href="/contact"
                           class="hidden sm:inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2.5 rounded-full transition shadow-sm">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="m12 .5c-6.615 0-12 4.398-12 9.803 0 4.841 4.27 8.897 10.035 9.608.391.084.922.258 1.057.593.12.301.079.771.038 1.073l-.164 1.013c-.045.301-.24 1.186 1.049.647 1.291-.539 6.916-4.103 9.436-7.023 1.724-1.952 2.549-3.965 2.549-6.311 0-5.405-5.385-9.803-12-9.803z"/></svg>
                            ปรึกษาฟรี คลิกเลย
                        </a>

                        <!-- Hamburger -->
                        <button @click="mobileOpen = !mobileOpen"
                                class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition">
                            <svg v-if="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div v-show="mobileOpen" class="lg:hidden border-t border-gray-100 bg-white">
                <div class="px-4 py-3 space-y-1">

                    <!-- ผลงาน mobile -->
                    <div>
                        <button @click="mobilePortfolioOpen = !mobilePortfolioOpen"
                                class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50">
                            ผลงาน
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': mobilePortfolioOpen }" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/></svg>
                        </button>
                        <div v-show="mobilePortfolioOpen" class="pl-4 mt-1 space-y-1">
                            <Link href="/portfolio" @click="closeMobile" class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">ผลงานทั้งหมด</Link>
                            <Link href="/portfolio?package=one-page" @click="closeMobile" class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">One Page Package</Link>
                            <Link href="/portfolio?package=starter" @click="closeMobile" class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">Starter Package</Link>
                            <Link href="/portfolio?package=business" @click="closeMobile" class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">Business Package</Link>
                            <Link href="/portfolio?package=e-commerce" @click="closeMobile" class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">E-Commerce Package</Link>
                        </div>
                    </div>

                    <!-- บริการและราคา mobile -->
                    <div>
                        <button @click="mobileServicesOpen = !mobileServicesOpen"
                                class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50">
                            บริการและราคา
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': mobileServicesOpen }" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/></svg>
                        </button>
                        <div v-show="mobileServicesOpen" class="pl-4 mt-1 space-y-1">
                            <Link href="/services/website" @click="closeMobile" class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">รับทำเว็บไซต์</Link>
                            <Link href="/services/google-ads" @click="closeMobile" class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md">รับทำ Google Ads</Link>
                        </div>
                    </div>

                    <Link href="/faq" @click="closeMobile" class="block px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md">คำถามที่พบบ่อย</Link>
                    <Link href="/contact" @click="closeMobile" class="block px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md">ติดต่อเรา</Link>

                    <div class="pt-2 pb-1">
                        <a href="/contact" @click="closeMobile"
                           class="flex items-center justify-center gap-2 w-full bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-3 rounded-full transition">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="m12 .5c-6.615 0-12 4.398-12 9.803 0 4.841 4.27 8.897 10.035 9.608.391.084.922.258 1.057.593.12.301.079.771.038 1.073l-.164 1.013c-.045.301-.24 1.186 1.049.647 1.291-.539 6.916-4.103 9.436-7.023 1.724-1.952 2.549-3.965 2.549-6.311 0-5.405-5.385-9.803-12-9.803z"/></svg>
                            ปรึกษาฟรี คลิกเลย
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- ── Page Content ─────────────────────────────────────────────────── -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- ── Footer ───────────────────────────────────────────────────────── -->
        <footer class="bg-gray-900 text-gray-300 font-kanit">

            <!-- Main Footer -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">

                    <!-- Brand Block -->
                    <div class="lg:col-span-1">
                        <!-- Logo dark or site name -->
                        <div class="mb-2">
                            <img v-if="site.logo_dark_url" :src="site.logo_dark_url" :alt="site.site_name" class="h-10 w-auto object-contain"/>
                            <img v-else-if="site.logo_url" :src="site.logo_url" :alt="site.site_name" class="h-10 w-auto object-contain"/>
                            <p v-else class="text-white text-xl font-bold">{{ site.site_name ?? 'DOSC Group' }}</p>
                        </div>
                        <p v-if="site.tagline" class="text-gray-400 text-sm leading-snug">{{ site.tagline }}</p>

                        <!-- Dynamic social icons -->
                        <div v-if="socialLinks.length" class="flex items-center flex-wrap gap-2 mt-5">
                            <a v-for="link in socialLinks" :key="link.id"
                               :href="link.url || '#'"
                               :target="link.url ? '_blank' : undefined"
                               rel="noopener noreferrer"
                               class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-800 text-gray-300 hover:text-white transition"
                               :style="`--hover-bg: ${link.color}`"
                               @mouseenter="$event.currentTarget.style.background = link.color"
                               @mouseleave="$event.currentTarget.style.background = ''">
                                <SocialIcon :platform="link.platform" :icon-type="link.icon_type"
                                            :icon-value="link.icon_value" :icon-url="link.icon_url" size="md"/>
                            </a>
                        </div>
                    </div>

                    <!-- Contact Block -->
                    <div>
                        <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">ติดต่อเรา</h4>
                        <ul class="space-y-3 text-sm">
                            <li v-if="site.phone" class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.338c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 4c.828 0 1.655.063 2.465.188C9.65 4.371 10.5 5.4 10.5 6.584v3.33c0 1.007-.603 1.927-1.548 2.308L7.3 12.92c.618 1.3 1.556 2.455 2.72 3.32l.703-1.65c.38-.946 1.3-1.549 2.308-1.549h3.33c1.184 0 2.213.85 2.396 2.035.125.81.188 1.637.188 2.465 0 .569-.044 1.127-.132 1.668-.09 1.174-1.076 2.085-2.254 2.085C9.11 22.5 1.5 14.89 1.5 5.5c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 3"/></svg>
                                <a :href="`tel:${site.phone}`" class="hover:text-white transition">{{ site.phone }}</a>
                            </li>
                            <li v-if="site.email" class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                <a :href="`mailto:${site.email}`" class="hover:text-white transition">{{ site.email }}</a>
                            </li>
                            <li v-if="site.address" class="flex items-start gap-3">
                                <svg class="w-4 h-4 text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                <a v-if="site.google_map_url" :href="site.google_map_url" target="_blank" rel="noopener"
                                   class="leading-relaxed hover:text-white transition">{{ site.address }}</a>
                                <span v-else class="leading-relaxed">{{ site.address }}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Links Block -->
                    <div>
                        <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">เมนู</h4>
                        <ul class="space-y-2.5 text-sm">
                            <li><Link href="/portfolio" class="hover:text-white transition">ผลงาน</Link></li>
                            <li><Link href="/services" class="hover:text-white transition">บริการและราคา</Link></li>
                            <li><Link href="/services/google-ads" class="hover:text-white transition">รับทำ Google Ads</Link></li>
                            <li><Link href="/faq" class="hover:text-white transition">คำถามที่พบบ่อย</Link></li>
                            <li><Link href="/blog" class="hover:text-white transition">บทความ</Link></li>
                            <li><Link href="/privacy-policy" class="hover:text-white transition">นโยบายความเป็นส่วนตัว</Link></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom Bar -->
            <div class="border-t border-gray-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-gray-500">
                    <span>&copy; {{ new Date().getFullYear() }} {{ site.site_name ?? 'DOSC Group' }}. All rights reserved.</span>
                    <Link href="/privacy-policy" class="hover:text-gray-300 transition">นโยบายความเป็นส่วนตัว</Link>
                </div>
            </div>
        </footer>

        <!-- Floating contact buttons -->
        <div class="fixed bottom-6 right-4 z-50 flex flex-col gap-3">
            <!-- LINE -->
            <a href="https://line.me/ti/p/@dosc" target="_blank" rel="noopener"
               class="w-12 h-12 rounded-full bg-green-500 hover:bg-green-400 shadow-lg flex items-center justify-center text-white transition-transform hover:scale-110"
               title="LINE @doscgroup">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                    <path d="m12 .5c-6.615 0-12 4.398-12 9.803 0 4.841 4.27 8.897 10.035 9.608.391.084.922.258 1.057.593.12.301.079.771.038 1.073l-.164 1.013c-.045.301-.24 1.186 1.049.647 1.291-.539 6.916-4.103 9.436-7.023 1.724-1.952 2.549-3.965 2.549-6.311 0-5.405-5.385-9.803-12-9.803z"/>
                </svg>
            </a>
            <!-- Phone -->
            <a v-if="site.phone" :href="`tel:${site.phone}`"
               class="w-12 h-12 rounded-full bg-red-700 hover:bg-red-600 shadow-lg flex items-center justify-center text-white transition-transform hover:scale-110"
               :title="site.phone">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.338c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 4c.828 0 1.655.063 2.465.188C9.65 4.371 10.5 5.4 10.5 6.584v3.33c0 1.007-.603 1.927-1.548 2.308L7.3 12.92c.618 1.3 1.556 2.455 2.72 3.32l.703-1.65c.38-.946 1.3-1.549 2.308-1.549h3.33c1.184 0 2.213.85 2.396 2.035.125.81.188 1.637.188 2.465 0 .569-.044 1.127-.132 1.668-.09 1.174-1.076 2.085-2.254 2.085C9.11 22.5 1.5 14.89 1.5 5.5c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 3"/>
                </svg>
            </a>
        </div>

    </div>
</template>
