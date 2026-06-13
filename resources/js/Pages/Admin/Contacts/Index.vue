<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    contacts:     { type: Object, default: () => ({}) },
    filters:      { type: Object, default: () => ({}) },
    statusCounts: { type: Object, default: () => ({}) },
});

const statusTabs = [
    { key: '',         label: 'ทั้งหมด',   count: 'all' },
    { key: 'unread',   label: 'ยังไม่อ่าน', count: 'unread' },
    { key: 'read',     label: 'อ่านแล้ว',   count: 'read' },
    { key: 'replied',  label: 'ตอบแล้ว',    count: 'replied' },
];

function filterByStatus(status) {
    router.get(route('admin.contacts'), { status: status || undefined }, { preserveScroll: true });
}

function deleteContact(contact) {
    if (!confirm(`ลบข้อความจาก "${contact.name}" ?`)) return;
    router.delete(route('admin.contacts.destroy', contact.id));
}

const statusStyle = {
    unread:  'bg-red-50 text-red-600 border-red-200',
    read:    'bg-blue-50 text-blue-600 border-blue-200',
    replied: 'bg-green-50 text-green-600 border-green-200',
};
const statusLabel = {
    unread:  'ยังไม่อ่าน',
    read:    'อ่านแล้ว',
    replied: 'ตอบแล้ว',
};
</script>

<template>
    <Head title="Admin — ข้อความติดต่อ" />
    <AdminLayout>

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">ข้อความติดต่อ</h1>
                <p class="text-sm text-gray-500 mt-0.5">ข้อความจากลูกค้าทั้งหมด</p>
            </div>
        </div>

        <!-- Status Filter Tabs -->
        <div class="flex gap-2 mb-5 flex-wrap">
            <button
                v-for="tab in statusTabs"
                :key="tab.key"
                @click="filterByStatus(tab.key)"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium border transition"
                :class="filters.status === tab.key || (!filters.status && tab.key === '')
                    ? 'bg-gray-900 text-white border-gray-900'
                    : 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'"
            >
                {{ tab.label }}
                <span class="text-xs px-1.5 py-0.5 rounded-full"
                      :class="filters.status === tab.key || (!filters.status && tab.key === '')
                          ? 'bg-white/20 text-white'
                          : 'bg-gray-100 text-gray-500'">
                    {{ statusCounts[tab.count] ?? 0 }}
                </span>
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ชื่อ</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">อีเมล / โทร</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ข้อความ</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">สถานะ</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-32">วันที่ส่ง</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-20">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-if="!contacts.data || contacts.data.length === 0">
                        <td colspan="6" class="px-4 py-12 text-center text-gray-400">ไม่มีข้อความในขณะนี้</td>
                    </tr>
                    <tr
                        v-for="contact in contacts.data"
                        :key="contact.id"
                        class="hover:bg-gray-50 transition-colors"
                        :class="contact.status === 'unread' ? 'font-semibold' : ''"
                    >
                        <!-- Name -->
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <span v-if="contact.status === 'unread'"
                                      class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></span>
                                <span class="text-gray-900">{{ contact.name }}</span>
                            </div>
                        </td>

                        <!-- Email / Phone -->
                        <td class="px-4 py-3">
                            <p class="text-gray-700">{{ contact.email }}</p>
                            <p v-if="contact.phone" class="text-gray-400 text-xs mt-0.5">{{ contact.phone }}</p>
                        </td>

                        <!-- Message preview -->
                        <td class="px-4 py-3 max-w-xs">
                            <p class="text-gray-600 text-xs line-clamp-2 font-normal">{{ contact.message }}</p>
                        </td>

                        <!-- Status -->
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border"
                                  :class="statusStyle[contact.status]">
                                {{ statusLabel[contact.status] }}
                            </span>
                        </td>

                        <!-- Date -->
                        <td class="px-4 py-3 text-gray-400 text-xs font-normal">
                            {{ new Date(contact.created_at).toLocaleDateString('th-TH', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                        </td>

                        <!-- Actions -->
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <Link :href="route('admin.contacts.show', contact.id)"
                                      class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                </Link>
                                <button @click="deleteContact(contact)"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-md transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="contacts.last_page > 1" class="flex justify-center gap-2 mt-6">
            <Link
                v-for="link in contacts.links"
                :key="link.label"
                :href="link.url ?? '#'"
                v-html="link.label"
                class="px-3 py-1.5 text-sm rounded-lg border transition"
                :class="link.active
                    ? 'bg-gray-900 text-white border-gray-900'
                    : link.url
                        ? 'bg-white text-gray-600 border-gray-200 hover:border-gray-300'
                        : 'bg-white text-gray-300 border-gray-100 pointer-events-none'"
            />
        </div>

    </AdminLayout>
</template>
