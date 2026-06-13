<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    blogs:      { type: Object, default: () => ({}) },
    categories: { type: Array,  default: () => [] },
    filters:    { type: Object, default: () => ({}) },
    counts:     { type: Object, default: () => ({}) },
});

const statusFilter = ref(props.filters.status ?? '');

const statusTabs = [
    { value: '',          label: 'ทั้งหมด',  key: 'all' },
    { value: 'published', label: 'เผยแพร่',  key: 'published' },
    { value: 'draft',     label: 'Draft',    key: 'draft' },
    { value: 'scheduled', label: 'ตั้งเวลา', key: 'scheduled' },
    { value: 'archived',  label: 'Archive',  key: 'archived' },
];

function filterByStatus(val) {
    statusFilter.value = val;
    router.get(route('admin.blog'), val ? { status: val } : {}, { preserveState: true, replace: true });
}

const statusBadge = {
    draft:     'bg-gray-100 text-gray-600',
    published: 'bg-green-100 text-green-700',
    scheduled: 'bg-blue-100 text-blue-700',
    archived:  'bg-amber-100 text-amber-700',
};
const statusLabel = { draft: 'Draft', published: 'เผยแพร่', scheduled: 'ตั้งเวลา', archived: 'Archive' };

function deleteBlog(blog) {
    if (!confirm(`ลบบทความ "${blog.title}" ?`)) return;
    router.delete(route('admin.blog.destroy', blog.id));
}

function toggleFeatured(blog) { router.patch(route('admin.blog.featured', blog.id)); }

function formatDate(d) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('th-TH', { day: 'numeric', month: 'short', year: 'numeric' });
}
</script>

<template>
    <Head title="Admin — บทความ" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">จัดการบทความ</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ counts.all ?? 0 }} บทความทั้งหมด</p>
            </div>
            <div class="flex gap-2">
                <Link :href="route('admin.blog.categories')"
                      class="px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    หมวดหมู่
                </Link>
                <Link :href="route('admin.blog.create')"
                      class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    เขียนบทความ
                </Link>
            </div>
        </div>

        <!-- Status tabs -->
        <div class="flex gap-1 mb-4 bg-gray-100 p-1 rounded-xl w-fit">
            <button v-for="tab in statusTabs" :key="tab.value"
                    @click="filterByStatus(tab.value)"
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition"
                    :class="statusFilter === tab.value ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'">
                {{ tab.label }}
                <span class="ml-1 text-xs opacity-60">({{ counts[tab.key] ?? 0 }})</span>
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">บทความ</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">หมวดหมู่</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">สถานะ</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">วันที่</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-if="!blogs.data?.length">
                        <td colspan="5" class="px-4 py-12 text-center text-gray-400">ยังไม่มีบทความ</td>
                    </tr>
                    <tr v-for="blog in blogs.data" :key="blog.id" class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-14 h-10 rounded-lg overflow-hidden bg-gray-100">
                                    <img v-if="blog.thumbnail_url || blog.cover_image_url"
                                         :src="blog.thumbnail_url ?? blog.cover_image_url"
                                         class="w-full h-full object-cover"/>
                                    <div v-else class="w-full h-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z"/></svg>
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <p class="font-medium text-gray-900 truncate max-w-xs">{{ blog.title }}</p>
                                        <span v-if="blog.is_featured" class="text-amber-400" title="ปักหมุด">★</span>
                                    </div>
                                    <p class="text-xs text-gray-400 truncate max-w-xs mt-0.5">{{ blog.excerpt }}</p>
                                    <p class="text-xs text-gray-300 mt-0.5">{{ blog.reading_time }} นาที · {{ blog.view_count }} วิว</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span v-if="blog.category"
                                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium text-white"
                                  :style="`background: ${blog.category.color}`">
                                {{ blog.category.name }}
                            </span>
                            <span v-else class="text-gray-300 text-xs">—</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                                  :class="statusBadge[blog.status]">
                                {{ statusLabel[blog.status] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ formatDate(blog.published_at ?? blog.created_at) }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button @click="toggleFeatured(blog)" :title="blog.is_featured ? 'เลิกปักหมุด' : 'ปักหมุด'"
                                        class="p-1.5 rounded-md transition"
                                        :class="blog.is_featured ? 'text-amber-400 hover:bg-amber-50' : 'text-gray-300 hover:text-amber-400 hover:bg-amber-50'">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
                                </button>
                                <Link :href="route('admin.blog.edit', blog.id)"
                                      class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                </Link>
                                <button @click="deleteBlog(blog)"
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
        <div v-if="blogs.last_page > 1" class="mt-4 flex justify-center gap-1">
            <Link v-for="link in blogs.links" :key="link.label"
                  :href="link.url ?? ''"
                  :class="['px-3 py-1.5 rounded-lg text-sm transition',
                      link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200',
                      !link.url ? 'opacity-40 pointer-events-none' : '']"
                  v-html="link.label"/>
        </div>
    </AdminLayout>
</template>
