<script setup>
import { ref, reactive, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    rolesData:  Array,   // [{ id, role, label, permissions:[], is_system }]
    menuKeys:   Array,
    menuLabels: Object,
    menuGroups: Array,   // [{ label, keys:[] }]
});

const page  = usePage();
const flash = computed(() => page.props.flash ?? {});

// ── Local state ────────────────────────────────────────────────────────────
// keyed by role slug
const localPerms = reactive({});
props.rolesData.forEach(r => {
    localPerms[r.role] = new Set(r.permissions ?? []);
});

// Watch for page refresh (rolesData changes)
// Not needed since Inertia replaces props on redirect

// ── Permissions toggle ─────────────────────────────────────────────────────
function toggle(role, key) {
    const set = localPerms[role];
    if (!set) return;
    set.has(key) ? set.delete(key) : set.add(key);
}

function isChecked(role, key) {
    const rd = props.rolesData.find(r => r.role === role);
    if (rd?.is_system) return true;
    return localPerms[role]?.has(key) ?? false;
}

function save(role) {
    router.post(route('admin.roles.update', role), {
        permissions: [...(localPerms[role] ?? [])],
    }, { preserveScroll: true });
}

// ── Add role panel ──────────────────────────────────────────────────────────
const addOpen  = ref(false);
const addLabel = ref('');
const addPerms = ref(new Set());
const addError = ref('');
const adding   = ref(false);

function toggleAddPerm(key) {
    addPerms.value.has(key) ? addPerms.value.delete(key) : addPerms.value.add(key);
}

function slugPreview(label) {
    return label ? label.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '') : '—';
}

function openAdd() {
    addLabel.value = '';
    addPerms.value = new Set();
    addError.value = '';
    addOpen.value  = true;
}

function copyFrom(role) {
    addPerms.value = new Set(localPerms[role] ?? []);
}

function submitAdd() {
    if (!addLabel.value.trim()) { addError.value = 'กรุณาระบุชื่อ Role'; return; }
    adding.value = true;
    router.post(route('admin.roles.store'), {
        label:       addLabel.value.trim(),
        permissions: [...addPerms.value],
    }, {
        preserveScroll: true,
        onSuccess: () => {
            addOpen.value = false;
            adding.value  = false;
            // sync localPerms for new role (will get from fresh rolesData after Inertia re-render)
        },
        onError: (errors) => {
            addError.value = Object.values(errors)[0] ?? 'เกิดข้อผิดพลาด';
            adding.value   = false;
        },
        onFinish: () => { adding.value = false; },
    });
}

// ── Delete role ────────────────────────────────────────────────────────────
const deleteTarget = ref(null);

function doDelete() {
    router.delete(route('admin.roles.destroy', deleteTarget.value.role), {
        preserveScroll: true,
        onSuccess: () => { deleteTarget.value = null; },
    });
}

// ── Computed summary ────────────────────────────────────────────────────────
function permCount(role) {
    const rd = props.rolesData.find(r => r.role === role);
    if (rd?.is_system) return props.menuKeys.length;
    return localPerms[role]?.size ?? 0;
}

const ROLE_BADGE = {
    super_admin: 'bg-red-100 text-red-800',
    admin:       'bg-amber-100 text-amber-800',
    manager:     'bg-blue-100 text-blue-800',
    staff:       'bg-gray-100 text-gray-700',
};

function roleBadge(role) {
    return ROLE_BADGE[role] ?? 'bg-purple-100 text-purple-800';
}
</script>

<template>
    <AdminLayout>
        <div>

            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">จัดการสิทธิ์</h1>
                    <p class="text-sm text-gray-500 mt-0.5">กำหนดเมนูที่แต่ละ Role เข้าถึงได้ · {{ rolesData.length }} roles ในระบบ</p>
                </div>
                <button @click="openAdd"
                        class="flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    เพิ่ม Role
                </button>
            </div>

            <!-- Flash -->
            <div v-if="flash.success" class="mb-5 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                {{ flash.success }}
            </div>
            <div v-if="flash.error" class="mb-5 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                {{ flash.error }}
            </div>

            <!-- Matrix table -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">

                        <!-- Role headers -->
                        <thead>
                            <tr class="border-b border-gray-200">
                                <!-- Empty corner -->
                                <th class="sticky left-0 z-10 bg-gray-50 w-52 min-w-[208px] px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider border-r border-gray-200">
                                    เมนู / สิทธิ์
                                </th>
                                <!-- Role columns -->
                                <th v-for="rd in rolesData" :key="rd.role"
                                    class="min-w-[140px] px-4 py-3 text-center border-r border-gray-200 last:border-r-0 bg-gray-50">
                                    <div class="flex flex-col items-center gap-1.5">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold" :class="roleBadge(rd.role)">
                                            {{ rd.label }}
                                        </span>
                                        <span class="text-[10px] text-gray-400 font-mono">{{ rd.role }}</span>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="text-[10px] text-gray-400">{{ permCount(rd.role) }}/{{ menuKeys.length }}</span>
                                            <button v-if="!rd.is_system"
                                                    @click="deleteTarget = rd"
                                                    class="p-0.5 text-gray-300 hover:text-red-500 transition rounded"
                                                    title="ลบ Role">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                                </svg>
                                            </button>
                                            <span v-else title="System role — ลบไม่ได้">
                                                <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <template v-for="group in menuGroups" :key="group.label">
                                <!-- Group header row -->
                                <tr class="bg-gray-50/70 border-t border-b border-gray-200">
                                    <td :colspan="rolesData.length + 1"
                                        class="sticky left-0 px-5 py-2 text-[11px] font-bold text-gray-500 uppercase tracking-widest">
                                        {{ group.label }}
                                    </td>
                                </tr>
                                <!-- Permission rows -->
                                <tr v-for="key in group.keys" :key="key"
                                    class="border-b border-gray-100 hover:bg-gray-50/50 transition-colors">
                                    <!-- Permission label (sticky) -->
                                    <td class="sticky left-0 z-10 bg-white px-5 py-3 font-medium text-gray-700 border-r border-gray-100 hover:bg-gray-50/50">
                                        {{ menuLabels[key] ?? key }}
                                    </td>
                                    <!-- Checkbox per role -->
                                    <td v-for="rd in rolesData" :key="rd.role"
                                        class="px-4 py-3 text-center border-r border-gray-100 last:border-r-0">
                                        <label class="inline-flex items-center justify-center cursor-pointer">
                                            <input type="checkbox"
                                                   :checked="isChecked(rd.role, key)"
                                                   :disabled="rd.is_system"
                                                   @change="toggle(rd.role, key)"
                                                   class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"/>
                                        </label>
                                    </td>
                                </tr>
                            </template>
                        </tbody>

                        <!-- Save buttons row -->
                        <tfoot>
                            <tr class="border-t-2 border-gray-200 bg-gray-50">
                                <td class="sticky left-0 bg-gray-50 px-5 py-3 text-xs text-gray-400 border-r border-gray-200">
                                    กดบันทึกเพื่อใช้งาน
                                </td>
                                <td v-for="rd in rolesData" :key="rd.role"
                                    class="px-4 py-3 text-center border-r border-gray-100 last:border-r-0">
                                    <button v-if="!rd.is_system"
                                            @click="save(rd.role)"
                                            class="px-3 py-1.5 text-xs font-semibold bg-gray-900 text-white rounded-lg hover:bg-gray-700 transition w-full">
                                        บันทึก
                                    </button>
                                    <span v-else class="text-[11px] text-gray-400">ล็อก</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Info note -->
            <div class="mt-5 rounded-xl bg-blue-50 border border-blue-200 px-5 py-4 text-sm text-blue-800">
                <p class="font-semibold mb-1.5">หมายเหตุ</p>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    <li>Super Admin (🔒) เข้าถึงได้ทุกส่วนเสมอ ไม่สามารถแก้ไขหรือลบได้</li>
                    <li>Role ที่มีผู้ใช้อยู่ไม่สามารถลบได้ — ต้องย้ายผู้ใช้ออกก่อน</li>
                    <li>การเปลี่ยนสิทธิ์มีผลทันทีเมื่อผู้ใช้นั้นเปิดหน้าใหม่</li>
                    <li>Dashboard เปิดให้ทุก Role เข้าถึงได้เสมอ ไม่ว่าจะตั้งค่าอย่างไร</li>
                </ul>
            </div>
        </div>

        <!-- ── Add Role Slide-over ──────────────────────────────────────── -->
        <Transition enter-active-class="transition duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100"
                    leave-active-class="transition duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="addOpen" class="fixed inset-0 bg-black/40 z-40" @click="addOpen = false"/>
        </Transition>

        <Transition enter-active-class="transition duration-200" enter-from-class="translate-x-full" enter-to-class="translate-x-0"
                    leave-active-class="transition duration-150" leave-from-class="translate-x-0" leave-to-class="translate-x-full">
            <div v-if="addOpen" class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl z-50 flex flex-col">

                <!-- Panel header -->
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h2 class="text-lg font-bold text-gray-900">เพิ่ม Role ใหม่</h2>
                    <button @click="addOpen = false" class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-6">

                    <!-- Error -->
                    <div v-if="addError" class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                        {{ addError }}
                    </div>

                    <!-- Label input -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">ชื่อ Role <span class="text-red-500">*</span></label>
                        <input v-model="addLabel" type="text" autofocus
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900"
                               placeholder="เช่น Content Editor, SEO Specialist"/>
                        <p class="text-xs text-gray-400 mt-1.5">
                            Role key:
                            <span class="font-mono text-gray-600">{{ slugPreview(addLabel) }}</span>
                        </p>
                    </div>

                    <!-- Copy from existing role -->
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">คัดลอกสิทธิ์จาก</p>
                        <div class="flex flex-wrap gap-2">
                            <button @click="addPerms = new Set()"
                                    class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 hover:bg-gray-50 transition text-gray-600">
                                เริ่มจากว่าง
                            </button>
                            <button v-for="rd in rolesData" :key="rd.role"
                                    @click="copyFrom(rd.role)"
                                    class="px-3 py-1.5 text-xs rounded-lg border hover:bg-gray-50 transition"
                                    :class="roleBadge(rd.role)">
                                {{ rd.label }}
                            </button>
                        </div>
                    </div>

                    <!-- Permissions -->
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-3">สิทธิ์ที่ได้รับ <span class="text-gray-400 font-normal">({{ addPerms.size }}/{{ menuKeys.length }})</span></p>
                        <div class="space-y-4">
                            <div v-for="group in menuGroups" :key="group.label">
                                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">{{ group.label }}</p>
                                <div class="grid grid-cols-1 gap-1">
                                    <label v-for="key in group.keys" :key="key"
                                           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox"
                                               :checked="addPerms.has(key)"
                                               @change="toggleAddPerm(key)"
                                               class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900 cursor-pointer"/>
                                        <span class="text-sm text-gray-700">{{ menuLabels[key] ?? key }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t flex justify-end gap-3">
                    <button @click="addOpen = false"
                            class="px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        ยกเลิก
                    </button>
                    <button @click="submitAdd" :disabled="adding"
                            class="px-5 py-2 text-sm bg-gray-900 text-white rounded-lg hover:bg-gray-700 transition disabled:opacity-50">
                        สร้าง Role
                    </button>
                </div>
            </div>
        </Transition>

        <!-- ── Delete Role Modal ────────────────────────────────────────── -->
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
                            <h3 class="font-bold text-gray-900">ยืนยันการลบ Role</h3>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-sm font-semibold" :class="roleBadge(deleteTarget?.role)">{{ deleteTarget?.label }}</span>
                                <span class="text-xs text-gray-400 font-mono">({{ deleteTarget?.role }})</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mb-5">
                        Role นี้จะถูกลบออกจากระบบ ผู้ใช้ที่ใช้ Role นี้อยู่จะไม่สามารถเข้าสู่ระบบ admin ได้จนกว่าจะเปลี่ยน Role
                    </p>
                    <div class="flex justify-end gap-3">
                        <button @click="deleteTarget = null"
                                class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            ยกเลิก
                        </button>
                        <button @click="doDelete"
                                class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            ลบ Role
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

    </AdminLayout>
</template>
