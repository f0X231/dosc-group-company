<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    contact: { type: Object, required: true },
});

const statusOptions = [
    { value: 'unread',  label: 'ยังไม่อ่าน' },
    { value: 'read',    label: 'อ่านแล้ว' },
    { value: 'replied', label: 'ตอบแล้ว' },
];

const statusStyle = {
    unread:  'bg-red-50 text-red-600 border-red-200',
    read:    'bg-blue-50 text-blue-600 border-blue-200',
    replied: 'bg-green-50 text-green-600 border-green-200',
};

function changeStatus(value) {
    router.patch(route('admin.contacts.status', props.contact.id), { status: value });
}
</script>

<template>
    <Head :title="`ข้อความจาก ${contact.name}`" />
    <AdminLayout>

        <!-- Back -->
        <div class="mb-6">
            <Link :href="route('admin.contacts')"
                  class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                กลับหน้ารายการ
            </Link>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">

            <!-- Message Content -->
            <div class="lg:col-span-2 space-y-5">

                <!-- Header Card -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">{{ contact.name }}</h1>
                            <div class="flex items-center flex-wrap gap-3 mt-1.5 text-sm text-gray-500">
                                <a :href="`mailto:${contact.email}`" class="hover:text-gray-800 transition">
                                    {{ contact.email }}
                                </a>
                                <span v-if="contact.phone" class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.338c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 4c.828 0 1.655.063 2.465.188C9.65 4.371 10.5 5.4 10.5 6.584v3.33c0 1.007-.603 1.927-1.548 2.308L7.3 12.92c.618 1.3 1.556 2.455 2.72 3.32l.703-1.65c.38-.946 1.3-1.549 2.308-1.549h3.33c1.184 0 2.213.85 2.396 2.035.125.81.188 1.637.188 2.465 0 .569-.044 1.127-.132 1.668-.09 1.174-1.076 2.085-2.254 2.085C9.11 22.5 1.5 14.89 1.5 5.5c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 3"/></svg>
                                    {{ contact.phone }}
                                </span>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold border flex-shrink-0"
                              :class="statusStyle[contact.status]">
                            {{ statusOptions.find(s => s.value === contact.status)?.label }}
                        </span>
                    </div>
                </div>

                <!-- Message Card -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">ข้อความ</h2>
                    <p class="text-gray-800 leading-relaxed whitespace-pre-line">{{ contact.message }}</p>
                </div>

            </div>

            <!-- Sidebar -->
            <div class="space-y-5">

                <!-- Status Control -->
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">อัปเดตสถานะ</h2>
                    <div class="space-y-2">
                        <button
                            v-for="opt in statusOptions"
                            :key="opt.value"
                            @click="changeStatus(opt.value)"
                            class="w-full text-left px-3.5 py-2.5 rounded-lg text-sm font-medium border transition"
                            :class="contact.status === opt.value
                                ? statusStyle[opt.value] + ' cursor-default'
                                : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100'"
                        >
                            <div class="flex items-center justify-between">
                                {{ opt.label }}
                                <svg v-if="contact.status === opt.value" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Meta Info -->
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">ข้อมูลเพิ่มเติม</h2>
                    <dl class="space-y-2.5 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-400">วันที่ส่ง</dt>
                            <dd class="text-gray-700 font-medium">
                                {{ new Date(contact.created_at).toLocaleDateString('th-TH', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-400">อัปเดตล่าสุดโดย</dt>
                            <dd class="text-gray-700 font-medium">{{ contact.updated_by?.name ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-400">IP Address</dt>
                            <dd class="text-gray-400 font-mono text-xs">{{ contact.ip_address ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm p-5 space-y-2">
                    <a :href="`mailto:${contact.email}`"
                       class="flex items-center justify-center gap-2 w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        ตอบกลับทางอีเมล
                    </a>
                    <a v-if="contact.phone" :href="`tel:${contact.phone}`"
                       class="flex items-center justify-center gap-2 w-full bg-green-500 hover:bg-green-600 text-white text-sm font-semibold py-2.5 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.338c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 4c.828 0 1.655.063 2.465.188C9.65 4.371 10.5 5.4 10.5 6.584v3.33c0 1.007-.603 1.927-1.548 2.308L7.3 12.92c.618 1.3 1.556 2.455 2.72 3.32l.703-1.65c.38-.946 1.3-1.549 2.308-1.549h3.33c1.184 0 2.213.85 2.396 2.035.125.81.188 1.637.188 2.465 0 .569-.044 1.127-.132 1.668-.09 1.174-1.076 2.085-2.254 2.085C9.11 22.5 1.5 14.89 1.5 5.5c0-1.178.91-2.164 2.085-2.255A17.97 17.97 0 0 1 6 3"/></svg>
                        โทรหาลูกค้า
                    </a>
                </div>

            </div>
        </div>

    </AdminLayout>
</template>
