<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col font-kanit">
        <!-- Navbar -->
        <header class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <Link href="/" class="font-bold text-xl text-gray-900">
                        DOSC Group
                    </Link>
                    <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-gray-700">
                        <Link href="/portfolio" class="hover:text-gray-900">ผลงาน</Link>
                        <Link href="/services" class="hover:text-gray-900">บริการและราคา</Link>
                        <Link href="/faq" class="hover:text-gray-900">คำถามที่พบบ่อย</Link>
                        <Link href="/blog" class="hover:text-gray-900">บทความ</Link>
                        <Link href="/contact" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            ติดต่อเรา
                        </Link>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-400 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div>
                        <h4 class="text-white font-semibold mb-4">DOSC Group</h4>
                        <p class="text-sm">รับทำเว็บไซต์และ Google Ads</p>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold mb-4">บริการ</h4>
                        <ul class="space-y-2 text-sm">
                            <li><Link href="/portfolio" class="hover:text-white">ผลงาน</Link></li>
                            <li><Link href="/services" class="hover:text-white">บริการและราคา</Link></li>
                            <li><Link href="/services/google-ads" class="hover:text-white">รับทำ Google Ads</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold mb-4">ข้อมูล</h4>
                        <ul class="space-y-2 text-sm">
                            <li><Link href="/faq" class="hover:text-white">คำถามที่พบบ่อย</Link></li>
                            <li><Link href="/blog" class="hover:text-white">บทความ</Link></li>
                            <li><Link href="/privacy-policy" class="hover:text-white">นโยบายความเป็นส่วนตัว</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold mb-4">ติดต่อ</h4>
                        <ul class="space-y-2 text-sm">
                            <li>LINE: @dosc</li>
                            <li><Link href="/contact" class="hover:text-white">ติดต่อเรา</Link></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm">
                    &copy; {{ new Date().getFullYear() }} DOSC Group. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</template>
