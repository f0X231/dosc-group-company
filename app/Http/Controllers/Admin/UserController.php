<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name', 'asc')
            ->select(['id', 'name', 'email', 'role', 'email_verified_at', 'created_at'])
            ->get();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role'     => ['required', Rule::in(['super_admin', 'admin', 'manager', 'staff'])],
        ]);

        if ($request->role === 'super_admin' && ! Auth::user()->isSuperAdmin()) {
            abort(403, 'เฉพาะ super admin เท่านั้นที่สามารถกำหนดสิทธิ์ super admin ได้');
        }

        User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => $request->password,
            'role'              => $request->role,
            'email_verified_at' => now(),
        ]);

        return back()->with('success', 'เพิ่มผู้ใช้เรียบร้อย');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'role'     => ['required', Rule::in(['super_admin', 'admin', 'manager', 'staff'])],
        ]);

        if ($request->role === 'super_admin' && ! Auth::user()->isSuperAdmin()) {
            abort(403, 'เฉพาะ super admin เท่านั้นที่สามารถกำหนดสิทธิ์ super admin ได้');
        }

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        return back()->with('success', 'อัปเดตผู้ใช้เรียบร้อย');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'ไม่สามารถลบบัญชีของตัวเองได้');
        }

        User::destroy($user->id);

        return back()->with('success', 'ลบผู้ใช้เรียบร้อย');
    }
}
