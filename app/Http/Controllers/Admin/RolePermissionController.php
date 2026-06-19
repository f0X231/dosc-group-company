<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RolePermissionController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Roles/Index', [
            'rolesData'  => RolePermission::allRoles(),
            'menuKeys'   => RolePermission::menuKeys(),
            'menuLabels' => RolePermission::menuLabels(),
            'menuGroups' => RolePermission::menuGroups(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'label'       => 'required|string|max:100',
            'permissions' => 'array',
            'permissions.*' => 'string|in:' . implode(',', RolePermission::menuKeys()),
        ]);

        $slug = RolePermission::slugify($request->label);

        if (empty($slug)) {
            return back()->with('error', 'ชื่อ Role ไม่ถูกต้อง');
        }

        if (RolePermission::where('role', $slug)->exists()) {
            return back()->with('error', "Role '{$slug}' มีอยู่แล้ว กรุณาใช้ชื่ออื่น");
        }

        RolePermission::create([
            'role'        => $slug,
            'label'       => $request->label,
            'permissions' => $request->permissions ?? [],
            'is_system'   => false,
        ]);

        return back()->with('success', "เพิ่ม Role '{$request->label}' เรียบร้อย");
    }

    public function update(Request $request, string $role): RedirectResponse
    {
        $rp = RolePermission::where('role', $role)->firstOrFail();

        if ($rp->is_system) {
            return back()->with('error', 'ไม่สามารถแก้ไขสิทธิ์ Super Admin ได้');
        }

        $request->validate([
            'permissions'   => 'array',
            'permissions.*' => 'string|in:' . implode(',', RolePermission::menuKeys()),
        ]);

        $rp->update(['permissions' => $request->permissions ?? []]);

        return back()->with('success', "บันทึกสิทธิ์ '{$rp->label}' เรียบร้อย");
    }

    public function destroy(string $role): RedirectResponse
    {
        $rp = RolePermission::where('role', $role)->firstOrFail();

        if ($rp->is_system) {
            return back()->with('error', 'ไม่สามารถลบ Role นี้ได้');
        }

        $count = User::where('role', $role)->count();
        if ($count > 0) {
            return back()->with('error', "ไม่สามารถลบได้ มีผู้ใช้ {$count} คนที่ใช้ Role นี้อยู่");
        }

        $rp->delete();

        return back()->with('success', "ลบ Role '{$rp->label}' เรียบร้อย");
    }
}
