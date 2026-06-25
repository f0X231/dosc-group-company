<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import SocialIcon from '@/Components/SocialIcon.vue';
import SeoHead from '@/Components/SeoHead.vue';

const mobileOpen = ref(false);
const portfolioOpen = ref(false);
const servicesOpen = ref(false);
const mobilePortfolioOpen = ref(false);
const mobileServicesOpen = ref(false);

let portfolioTimer = null;
let servicesTimer  = null;

function openPortfolio()  { clearTimeout(portfolioTimer); portfolioOpen.value = true; }
function closePortfolio() { portfolioTimer = setTimeout(() => { portfolioOpen.value = false; }, 200); }
function openServices()   { clearTimeout(servicesTimer);  servicesOpen.value  = true; }
function closeServices()  { servicesTimer  = setTimeout(() => { servicesOpen.value  = false; }, 200); }

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
    <SeoHead />
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
                             @mouseenter="openPortfolio"
                             @mouseleave="closePortfolio">
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
                             @mouseenter="openServices"
                             @mouseleave="closeServices">
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
        <footer class="bg-gray-950 text-gray-400 font-kanit overflow-hidden">

            <!-- CTA Band -->
            <div class="relative bg-red-800 overflow-hidden">
                <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-red-600 opacity-30 blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-red-950 opacity-60 blur-3xl pointer-events-none"></div>
                <div class="absolute inset-0 opacity-[0.07]"
                     style="background-image: radial-gradient(circle, #fff 1.5px, transparent 1.5px); background-size: 28px 28px;"></div>

                <div class="relative max-w-7xl mx-auto px-6 lg:px-8 py-14 flex flex-col lg:flex-row items-center justify-between gap-8">
                    <div>
                        <p class="text-red-300 text-xs font-semibold uppercase tracking-widest mb-2">เริ่มต้นได้วันนี้</p>
                        <h2 class="text-3xl lg:text-4xl font-bold text-white leading-tight">
                            พร้อมสร้างเว็บไซต์<br class="hidden sm:block">ที่ใช่สำหรับธุรกิจคุณ?
                        </h2>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-3 flex-shrink-0">
                        <a :href="site.phone ? `tel:${site.phone}` : '/contact'"
                           class="inline-flex items-center gap-2.5 bg-white text-red-800 font-bold px-8 py-4 rounded-full text-base shadow-2xl hover:bg-red-50 hover:scale-105 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.338c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 4c.828 0 1.655.063 2.465.188C9.65 4.371 10.5 5.4 10.5 6.584v3.33c0 1.007-.603 1.927-1.548 2.308L7.3 12.92c.618 1.3 1.556 2.455 2.72 3.32l.703-1.65c.38-.946 1.3-1.549 2.308-1.549h3.33c1.184 0 2.213.85 2.396 2.035.125.81.188 1.637.188 2.465 0 .569-.044 1.127-.132 1.668-.09 1.174-1.076 2.085-2.254 2.085C9.11 22.5 1.5 14.89 1.5 5.5c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 3"/></svg>
                            โทรปรึกษาฟรี
                        </a>
                        <Link href="/contact"
                           class="inline-flex items-center gap-2 border-2 border-white/30 text-white font-semibold px-7 py-4 rounded-full text-base hover:border-white hover:bg-white/10 transition-all">
                            ส่งข้อความหาเรา
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Gradient accent line -->
            <div class="h-px bg-gradient-to-r from-red-700 via-red-800/50 to-transparent"></div>

            <!-- Main Footer Content -->
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20">
                <!-- Subtle dot pattern -->
                <div class="absolute inset-0 opacity-[0.025] pointer-events-none"
                     style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 36px 36px;"></div>

                <div class="relative grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">

                    <!-- Brand Block -->
                    <div class="lg:col-span-2">
                        <div class="mb-4">
                            <img v-if="site.logo_dark_url" :src="site.logo_dark_url" :alt="site.site_name" class="h-11 w-auto object-contain"/>
                            <img v-else-if="site.logo_url" :src="site.logo_url" :alt="site.site_name" class="h-11 w-auto object-contain"/>
                            <p v-else class="text-white text-2xl font-bold tracking-tight">{{ site.site_name ?? 'DOSC Group' }}</p>
                        </div>
                        <p v-if="site.tagline" class="text-gray-500 text-sm leading-relaxed max-w-sm mb-7">{{ site.tagline }}</p>

                        <!-- Social Icons -->
                        <div v-if="socialLinks.length" class="flex items-center flex-wrap gap-2.5">
                            <a v-for="link in socialLinks" :key="link.id"
                               :href="link.url || '#'"
                               :target="link.url ? '_blank' : undefined"
                               rel="noopener noreferrer"
                               class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-900 border border-gray-800 text-gray-500 hover:text-white hover:border-red-700 hover:bg-red-900/30 transition-all"
                               @mouseenter="$event.currentTarget.style.borderColor = link.color; $event.currentTarget.style.color = link.color"
                               @mouseleave="$event.currentTarget.style.borderColor = ''; $event.currentTarget.style.color = ''">
                                <SocialIcon :platform="link.platform" :icon-type="link.icon_type"
                                            :icon-value="link.icon_value" :icon-url="link.icon_url" size="md"/>
                            </a>
                        </div>
                    </div>

                    <!-- Contact Block -->
                    <div>
                        <h4 class="text-white font-bold text-xs uppercase tracking-widest mb-6 flex items-center gap-2.5">
                            <span class="w-6 h-0.5 bg-red-600 rounded-full"></span>
                            ติดต่อเรา
                        </h4>
                        <ul class="space-y-4 text-sm">
                            <li v-if="site.phone" class="flex items-center gap-3 group">
                                <div class="w-9 h-9 flex-shrink-0 rounded-lg bg-red-950/60 border border-red-900/40 flex items-center justify-center group-hover:bg-red-800 group-hover:border-red-700 transition-all">
                                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.338c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 4c.828 0 1.655.063 2.465.188C9.65 4.371 10.5 5.4 10.5 6.584v3.33c0 1.007-.603 1.927-1.548 2.308L7.3 12.92c.618 1.3 1.556 2.455 2.72 3.32l.703-1.65c.38-.946 1.3-1.549 2.308-1.549h3.33c1.184 0 2.213.85 2.396 2.035.125.81.188 1.637.188 2.465 0 .569-.044 1.127-.132 1.668-.09 1.174-1.076 2.085-2.254 2.085C9.11 22.5 1.5 14.89 1.5 5.5c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 3"/></svg>
                                </div>
                                <a :href="`tel:${site.phone}`" class="hover:text-white transition-colors">{{ site.phone }}</a>
                            </li>
                            <li v-if="site.email" class="flex items-center gap-3 group">
                                <div class="w-9 h-9 flex-shrink-0 rounded-lg bg-red-950/60 border border-red-900/40 flex items-center justify-center group-hover:bg-red-800 group-hover:border-red-700 transition-all">
                                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                </div>
                                <a :href="`mailto:${site.email}`" class="hover:text-white transition-colors break-all">{{ site.email }}</a>
                            </li>
                            <li v-if="site.address" class="flex items-start gap-3 group">
                                <div class="w-9 h-9 flex-shrink-0 rounded-lg bg-red-950/60 border border-red-900/40 flex items-center justify-center group-hover:bg-red-800 group-hover:border-red-700 transition-all mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                </div>
                                <a v-if="site.google_map_url" :href="site.google_map_url" target="_blank" rel="noopener"
                                   class="leading-relaxed hover:text-white transition-colors">{{ site.address }}</a>
                                <span v-else class="leading-relaxed">{{ site.address }}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Links Block -->
                    <div>
                        <h4 class="text-white font-bold text-xs uppercase tracking-widest mb-6 flex items-center gap-2.5">
                            <span class="w-6 h-0.5 bg-red-600 rounded-full"></span>
                            เมนู
                        </h4>
                        <ul class="space-y-3 text-sm">
                            <li v-for="(item, i) in [
                                { href: '/portfolio', label: 'ผลงาน' },
                                { href: '/services', label: 'บริการและราคา' },
                                { href: '/services/google-ads', label: 'รับทำ Google Ads' },
                                { href: '/faq', label: 'คำถามที่พบบ่อย' },
                                { href: '/blog', label: 'บทความ' },
                                { href: '/privacy-policy', label: 'นโยบายความเป็นส่วนตัว' },
                            ]" :key="i">
                                <Link :href="item.href"
                                      class="inline-flex items-center gap-2 group hover:text-white transition-colors">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-700 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0"></span>
                                    <span class="group-hover:translate-x-0.5 transition-transform">{{ item.label }}</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom Bar -->
            <div class="border-t border-gray-800/50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <span class="text-xs text-gray-600">&copy; {{ new Date().getFullYear() }} {{ site.site_name ?? 'DOSC Group' }}. All rights reserved.</span>
                    <div class="flex items-center gap-5 text-xs text-gray-600">
                        <span>Designed &amp; Built by <span class="text-gray-500 font-medium">DOSC Group</span></span>
                        <Link href="/privacy-policy" class="hover:text-gray-400 transition-colors">นโยบายความเป็นส่วนตัว</Link>
                    </div>
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
