<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const unreadCount = computed(() => page.props.unreadContactsCount ?? 0);
const currentPath = computed(() => page.url);

function isActive(path) {
    return currentPath.value.startsWith(path);
}
</script>

<template>
    <div class="min-h-screen flex bg-gray-100">

        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-gray-300 flex flex-col flex-shrink-0">
            <div class="h-16 flex items-center px-6 border-b border-gray-800">
                <Link href="/admin" class="text-white font-bold text-lg">Admin Panel</Link>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-1 text-sm">
                <Link href="/admin"
                      class="flex items-center gap-3 px-3 py-2 rounded-md transition"
                      :class="currentPath === '/admin' ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 hover:text-white'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>
                    Dashboard
                </Link>
                <Link href="/admin/hero-banner"
                      class="flex items-center gap-3 px-3 py-2 rounded-md transition"
                      :class="isActive('/admin/hero-banner') ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 hover:text-white'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                    Hero Banner
                </Link>
                <Link href="/admin/portfolio"
                      class="flex items-center gap-3 px-3 py-2 rounded-md transition"
                      :class="isActive('/admin/portfolio') ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 hover:text-white'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                    จัดการผลงาน
                </Link>
                <Link href="/admin/blog"
                      class="flex items-center gap-3 px-3 py-2 rounded-md transition"
                      :class="isActive('/admin/blog') ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 hover:text-white'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z"/></svg>
                    จัดการบทความ
                </Link>
                <Link href="/admin/packages"
                      class="flex items-center gap-3 px-3 py-2 rounded-md transition"
                      :class="isActive('/admin/packages') ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 hover:text-white'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
                    จัดการแพ็กเกจ
                </Link>
                <Link href="/admin/partners"
                      class="flex items-center gap-3 px-3 py-2 rounded-md transition"
                      :class="isActive('/admin/partners') ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 hover:text-white'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
                    พาร์ทเนอร์ / ลูกค้า
                </Link>
                <Link href="/admin/testimonials"
                      class="flex items-center gap-3 px-3 py-2 rounded-md transition"
                      :class="isActive('/admin/testimonials') ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 hover:text-white'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
                    รีวิวจากลูกค้า
                </Link>
                <Link href="/admin/faq"
                      class="flex items-center gap-3 px-3 py-2 rounded-md transition"
                      :class="isActive('/admin/faq') ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 hover:text-white'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z"/></svg>
                    จัดการ FAQ
                </Link>
                <Link href="/admin/contacts"
                      class="flex items-center justify-between px-3 py-2 rounded-md transition"
                      :class="isActive('/admin/contacts') ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 hover:text-white'">
                    <span class="flex items-center gap-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        ข้อความติดต่อ
                    </span>
                    <span v-if="unreadCount > 0"
                          class="bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[20px] text-center">
                        {{ unreadCount > 99 ? '99+' : unreadCount }}
                    </span>
                </Link>
            </nav>
            <div class="px-4 py-4 border-t border-gray-800 text-xs">
                <Link href="/" class="hover:text-white">กลับหน้าเว็บ &rarr;</Link>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top bar -->
            <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6">
                <div></div>
                <div class="flex items-center gap-4">

                    <!-- Notification Bell -->
                    <Link href="/admin/contacts"
                          class="relative p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-full transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
                        </svg>
                        <!-- Badge -->
                        <span v-if="unreadCount > 0"
                              class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full">
                            {{ unreadCount > 9 ? '9+' : unreadCount }}
                        </span>
                    </Link>

                    <!-- User -->
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                            {{ (page.props.auth?.user?.name ?? 'A')[0].toUpperCase() }}
                        </div>
                        {{ page.props.auth?.user?.name ?? 'Admin' }}
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6 overflow-auto">
                <slot />
            </main>
        </div>
    </div>
</template>
