<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    users: Array,
});

const page = usePage();
const currentUserId = computed(() => page.props.auth?.user?.id);

const ROLE_LABELS = {
    super_admin: 'Super Admin',
    admin: 'Admin',
    manager: 'Manager',
    staff: 'Staff',
};

const ROLE_COLORS = {
    super_admin: 'bg-red-100 text-red-800',
    admin: 'bg-amber-100 text-amber-800',
    manager: 'bg-blue-100 text-blue-800',
    staff: 'bg-gray-100 text-gray-700',
};

// ── Panel ──────────────────────────────────────────────────────────────────
const panelOpen  = ref(false);
const editTarget = ref(null);

const form = useForm({
    name:     '',
    email:    '',
    password: '',
    role:     'staff',
});

function openCreate() {
    editTarget.value = null;
    form.reset();
    form.role = 'staff';
    panelOpen.value = true;
}

function openEdit(user) {
    editTarget.value = user;
    form.name     = user.name;
    form.email    = user.email;
    form.password = '';
    form.role     = user.role;
    panelOpen.value = true;
}

function closePanel() {
    panelOpen.value = false;
    form.reset();
}

function submit() {
    if (editTarget.value) {
        form.post(route('admin.users.update', editTarget.value.id), {
            onSuccess: closePanel,
        });
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: closePanel,
        });
    }
}

// ── Delete ─────────────────────────────────────────────────────────────────
const deleteTarget = ref(null);

function confirmDelete(user) {
    deleteTarget.value = user;
}

function doDelete() {
    router.delete(route('admin.users.destroy', deleteTarget.value.id), {
        onSuccess: () => { deleteTarget.value = null; },
    });
}

// ── Flash ──────────────────────────────────────────────────────────────────
const flash = computed(() => page.props.flash ?? {});
</script>

<template>
    <AdminLayout>
        <div class="max-w-5xl mx-auto">

            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">จัดการผู้ใช้</h1>
                    <p class="text-sm text-gray-500 mt-0.5">{{ users.length }} บัญชีในระบบ</p>
                </div>
                <button @click="openCreate"
                        class="flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    เพิ่มผู้ใช้
                </button>
            </div>

            <!-- Flash -->
            <div v-if="flash.success" class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                {{ flash.success }}
            </div>
            <div v-if="flash.error" class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                {{ flash.error }}
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">ชื่อ</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">อีเมล</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">สิทธิ์</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">วันที่เพิ่ม</th>
                            <th class="px-5 py-3"/>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 transition">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 flex-shrink-0">
                                        {{ user.name[0]?.toUpperCase() }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ user.name }}</p>
                                        <p v-if="user.id === currentUserId" class="text-xs text-blue-500">(บัญชีของคุณ)</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">{{ user.email }}</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold" :class="ROLE_COLORS[user.role]">
                                    {{ ROLE_LABELS[user.role] ?? user.role }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-400">
                                {{ new Date(user.created_at).toLocaleDateString('th-TH') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openEdit(user)"
                                            class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                                        </svg>
                                    </button>
                                    <button @click="confirmDelete(user)"
                                            :disabled="user.id === currentUserId"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition disabled:opacity-30 disabled:cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!users.length">
                            <td colspan="5" class="px-5 py-10 text-center text-gray-400">ยังไม่มีผู้ใช้ในระบบ</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── Slide-over Panel ─────────────────────────────────────────── -->
        <Transition
            enter-active-class="transition duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0">
            <div v-if="panelOpen" class="fixed inset-0 bg-black/40 z-40" @click="closePanel"/>
        </Transition>

        <Transition
            enter-active-class="transition duration-200"
            enter-from-class="translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transition duration-150"
            leave-from-class="translate-x-0"
            leave-to-class="translate-x-full">
            <div v-if="panelOpen" class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl z-50 flex flex-col">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h2 class="text-lg font-bold text-gray-900">
                        {{ editTarget ? 'แก้ไขผู้ใช้' : 'เพิ่มผู้ใช้ใหม่' }}
                    </h2>
                    <button @click="closePanel" class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submit" class="flex-1 overflow-y-auto p-6 space-y-5">

                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">ชื่อ <span class="text-red-500">*</span></label>
                        <input v-model="form.name" type="text" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900"
                               placeholder="ชื่อผู้ใช้"/>
                        <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">อีเมล <span class="text-red-500">*</span></label>
                        <input v-model="form.email" type="email" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900"
                               placeholder="email@example.com"/>
                        <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            รหัสผ่าน
                            <span v-if="!editTarget" class="text-red-500">*</span>
                            <span v-else class="text-gray-400 font-normal">(เว้นว่างถ้าไม่ต้องการเปลี่ยน)</span>
                        </label>
                        <input v-model="form.password" type="password" :required="!editTarget"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900"
                               placeholder="อย่างน้อย 8 ตัวอักษร"/>
                        <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">สิทธิ์ <span class="text-red-500">*</span></label>
                        <select v-model="form.role" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 bg-white">
                            <option v-for="(label, role) in ROLE_LABELS" :key="role" :value="role">{{ label }}</option>
                        </select>
                        <p v-if="form.errors.role" class="text-red-500 text-xs mt-1">{{ form.errors.role }}</p>
                    </div>

                    <!-- Role description -->
                    <div class="rounded-lg bg-gray-50 border border-gray-200 p-4 text-xs text-gray-600 space-y-1.5">
                        <p class="font-semibold text-gray-700 mb-2">คำอธิบายสิทธิ์</p>
                        <p><span class="font-semibold text-red-700">Super Admin</span> — เข้าถึงได้ทุกส่วน รวมถึงจัดการผู้ใช้และสิทธิ์</p>
                        <p><span class="font-semibold text-amber-700">Admin</span> — จัดการเนื้อหาทั้งหมด ยกเว้นผู้ใช้และสิทธิ์</p>
                        <p><span class="font-semibold text-blue-700">Manager</span> — จัดการเนื้อหาหน้าเว็บ (Banner, Service, Portfolio, Blog ฯลฯ)</p>
                        <p><span class="font-semibold text-gray-700">Staff</span> — ดูและจัดการข้อความติดต่อและ FAQ</p>
                    </div>
                </form>

                <div class="px-6 py-4 border-t flex justify-end gap-3">
                    <button type="button" @click="closePanel"
                            class="px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        ยกเลิก
                    </button>
                    <button @click="submit" :disabled="form.processing"
                            class="px-5 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-700 transition disabled:opacity-50">
                        {{ editTarget ? 'บันทึกการเปลี่ยนแปลง' : 'เพิ่มผู้ใช้' }}
                    </button>
                </div>
            </div>
        </Transition>

        <!-- ── Delete Modal ──────────────────────────────────────────────── -->
        <Transition enter-active-class="transition duration-150" enter-from-class="opacity-0" enter-to-class="opacity-100"
                    leave-active-class="transition duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="deleteTarget" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">ยืนยันการลบผู้ใช้</h3>
                            <p class="text-sm text-gray-500">{{ deleteTarget?.name }}</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mb-5">การลบผู้ใช้นี้ไม่สามารถย้อนกลับได้</p>
                    <div class="flex justify-end gap-3">
                        <button @click="deleteTarget = null"
                                class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            ยกเลิก
                        </button>
                        <button @click="doDelete"
                                class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            ลบผู้ใช้
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </AdminLayout>
</template>
