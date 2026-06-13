<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
    <div class="min-h-screen flex bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-gray-300 flex flex-col">
            <div class="h-16 flex items-center px-6 border-b border-gray-800">
                <Link href="/admin" class="text-white font-bold text-lg">Admin Panel</Link>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-1 text-sm">
                <Link href="/admin" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 hover:text-white">
                    Dashboard
                </Link>
                <Link href="/admin/portfolio" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 hover:text-white">
                    จัดการผลงาน
                </Link>
                <Link href="/admin/blog" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 hover:text-white">
                    จัดการบทความ
                </Link>
                <Link href="/admin/contacts" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-800 hover:text-white">
                    ข้อความติดต่อ
                </Link>
            </nav>
            <div class="px-4 py-4 border-t border-gray-800 text-xs">
                <Link href="/" class="hover:text-white">กลับหน้าเว็บ &rarr;</Link>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top bar -->
            <header class="h-16 bg-white shadow-sm flex items-center justify-end px-6">
                <span class="text-sm text-gray-600">{{ page.props.auth?.user?.name ?? 'Admin' }}</span>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
