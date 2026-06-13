<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SiteSettingController extends Controller
{
    public function __construct(private SupabaseStorageService $storage) {}

    public function index()
    {
        return Inertia::render('Admin/Settings/Index', [
            'site'        => SiteSetting::current(),
            'socialLinks' => SocialLink::orderBy('sort_order')->get(),
        ]);
    }

    // ─── General + Contact settings ──────────────────────────────────────────

    public function saveGeneral(Request $request)
    {
        $data = $request->validate([
            'site_name'        => 'required|string|max:100',
            'tagline'          => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:160',
        ]);

        $site = SiteSetting::current();
        $site->update($data);

        $this->handleImageUpload($request, $site, 'logo',             'site/logo');
        $this->handleImageUpload($request, $site, 'logo_dark',        'site/logo-dark');
        $this->handleImageUpload($request, $site, 'favicon',          'site/favicon');
        $this->handleImageUpload($request, $site, 'default_og_image', 'site/og-image');

        return back()->with('success', 'บันทึกข้อมูลทั่วไปเรียบร้อยแล้ว');
    }

    public function saveContact(Request $request)
    {
        $data = $request->validate([
            'phone'            => 'nullable|string|max:20',
            'phone_secondary'  => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:100',
            'email_secondary'  => 'nullable|email|max:100',
            'address'          => 'nullable|string',
            'google_map_url'   => 'nullable|string|max:500',
            'google_map_embed' => 'nullable|string',
        ]);

        SiteSetting::current()->update($data);

        return back()->with('success', 'บันทึกข้อมูลติดต่อเรียบร้อยแล้ว');
    }

    // ─── Social links ─────────────────────────────────────────────────────────

    public function updateSocial(Request $request, SocialLink $socialLink)
    {
        $data = $request->validate([
            'label'      => 'required|string|max:50',
            'url'        => 'nullable|string|max:500',
            'icon_type'  => 'required|in:builtin,emoji,image',
            'icon_value' => 'nullable|string|max:100',
            'color'      => 'required|string|max:7',
            'is_active'  => 'boolean',
        ]);

        $socialLink->update($data);

        if ($request->hasFile('icon_image')) {
            $request->validate(['icon_image' => 'file|mimes:png,jpg,jpeg,svg,webp|max:512']);
            if ($socialLink->icon_path) $this->storage->delete($socialLink->icon_path);
            $file     = $request->file('icon_image');
            $path     = "site/social/{$socialLink->platform}.{$file->getClientOriginalExtension()}";
            $uploaded = $this->storage->upload($file, $path);
            $socialLink->update(['icon_path' => $uploaded['path'], 'icon_url' => $uploaded['url']]);
        }

        return back()->with('success', 'อัปเดต social link เรียบร้อยแล้ว');
    }

    public function storeSocial(Request $request)
    {
        $data = $request->validate([
            'platform'   => 'required|string|max:30',
            'label'      => 'required|string|max:50',
            'url'        => 'nullable|string|max:500',
            'icon_type'  => 'required|in:builtin,emoji,image',
            'icon_value' => 'nullable|string|max:100',
            'color'      => 'required|string|max:7',
            'is_active'  => 'boolean',
        ]);

        $link = SocialLink::create(array_merge($data, [
            'sort_order' => SocialLink::max('sort_order') + 1,
        ]));

        if ($request->hasFile('icon_image')) {
            $request->validate(['icon_image' => 'file|mimes:png,jpg,jpeg,svg,webp|max:512']);
            $file     = $request->file('icon_image');
            $path     = "site/social/{$link->platform}-{$link->id}.{$file->getClientOriginalExtension()}";
            $uploaded = $this->storage->upload($file, $path);
            $link->update(['icon_path' => $uploaded['path'], 'icon_url' => $uploaded['url']]);
        }

        return back()->with('success', 'เพิ่ม social link เรียบร้อยแล้ว');
    }

    public function destroySocial(SocialLink $socialLink)
    {
        if ($socialLink->icon_path) $this->storage->delete($socialLink->icon_path);
        $socialLink->delete();
        return back()->with('success', 'ลบ social link เรียบร้อยแล้ว');
    }

    public function reorderSocial(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        foreach ($request->ids as $order => $id) {
            SocialLink::where('id', $id)->update(['sort_order' => $order]);
        }
        return response()->json(['ok' => true]);
    }

    // ─── Helper ───────────────────────────────────────────────────────────────

    private function handleImageUpload(Request $request, SiteSetting $site, string $field, string $storagePath): void
    {
        if (! $request->hasFile($field)) return;

        $request->validate([$field => 'file|mimes:png,jpg,jpeg,webp,svg,ico|max:2048']);

        $pathField = "{$field}_path";
        $urlField  = "{$field}_url";

        if ($site->$pathField) $this->storage->delete($site->$pathField);

        $file     = $request->file($field);
        $path     = "{$storagePath}.{$file->getClientOriginalExtension()}";
        $uploaded = $this->storage->upload($file, $path);

        $site->update([$pathField => $uploaded['path'], $urlField => $uploaded['url']]);
    }
}
