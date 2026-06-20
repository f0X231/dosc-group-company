<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSeo;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PageSeoController extends Controller
{
    public function __construct(private SupabaseStorageService $storage) {}

    public function index()
    {
        return Inertia::render('Admin/Seo/Index', [
            'pages' => PageSeo::orderBy('id')->get(),
        ]);
    }

    public function update(Request $request, PageSeo $pageSeo)
    {
        $data = $request->validate([
            'meta_title'       => 'nullable|string|max:100',
            'meta_description' => 'nullable|string|max:300',
            'og_title'         => 'nullable|string|max:100',
            'og_description'   => 'nullable|string|max:300',
            'robots'           => ['required', Rule::in(['index,follow', 'noindex,follow', 'noindex,nofollow'])],
            'canonical_url'    => 'nullable|url|max:500',
            'schema_json'      => 'nullable|string|max:10000',
        ]);

        if (!empty($data['schema_json'])) {
            json_decode($data['schema_json']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['schema_json' => 'Schema JSON ไม่ถูกต้อง (ไม่ใช่ JSON ที่ valid)'])->withInput();
            }
        }

        $pageSeo->update($data);

        if ($request->hasFile('og_image')) {
            $request->validate(['og_image' => 'file|image|mimes:jpg,jpeg,png,webp|max:2048']);
            if ($pageSeo->og_image_path) {
                $this->storage->delete($pageSeo->og_image_path);
            }
            $file     = $request->file('og_image');
            $ext      = $file->getClientOriginalExtension();
            $key      = str_replace(['.', '/'], '_', $pageSeo->page_key);
            $path     = "seo/og-{$key}.{$ext}";
            $uploaded = $this->storage->upload($file, $path);
            $pageSeo->update([
                'og_image_path' => $uploaded['path'],
                'og_image_url'  => $uploaded['url'],
            ]);
        }

        PageSeo::clearCache($pageSeo->page_key);

        return back()->with('success', 'บันทึก SEO เรียบร้อยแล้ว');
    }

    public function deleteOgImage(PageSeo $pageSeo)
    {
        if ($pageSeo->og_image_path) {
            $this->storage->delete($pageSeo->og_image_path);
            $pageSeo->update(['og_image_path' => null, 'og_image_url' => null]);
            PageSeo::clearCache($pageSeo->page_key);
        }

        return back()->with('success', 'ลบรูป OG Image เรียบร้อย');
    }
}
