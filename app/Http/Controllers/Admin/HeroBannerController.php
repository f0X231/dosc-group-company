<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroBanner;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HeroBannerController extends Controller
{
    public function __construct(private SupabaseStorageService $storage) {}

    public function index()
    {
        return Inertia::render('Admin/HeroBanner/Index', [
            'banners' => HeroBanner::orderBy('sort_order')->orderByDesc('id')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/HeroBanner/Form', ['banner' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $media = $this->uploadMedia($request);

        HeroBanner::create(array_merge($data, $media, [
            'sort_order'  => HeroBanner::max('sort_order') + 1,
            'created_by'  => Auth::id(),
            'updated_by'  => Auth::id(),
        ]));

        return redirect()->route('admin.hero-banner')->with('success', 'สร้าง Hero Banner เรียบร้อยแล้ว');
    }

    public function edit(HeroBanner $heroBanner)
    {
        return Inertia::render('Admin/HeroBanner/Form', ['banner' => $heroBanner]);
    }

    public function update(Request $request, HeroBanner $heroBanner)
    {
        $data  = $this->validated($request, $heroBanner);
        $media = $this->uploadMedia($request, $heroBanner);

        $heroBanner->update(array_merge($data, $media, ['updated_by' => Auth::id()]));

        return redirect()->route('admin.hero-banner')->with('success', 'อัปเดต Hero Banner เรียบร้อยแล้ว');
    }

    public function destroy(HeroBanner $heroBanner)
    {
        if ($heroBanner->media_path) {
            $this->storage->delete($heroBanner->media_path);
        }
        $heroBanner->delete();

        return back()->with('success', 'ลบ Hero Banner เรียบร้อยแล้ว');
    }

    public function toggleStatus(HeroBanner $heroBanner)
    {
        $heroBanner->update([
            'status'     => $heroBanner->status === 'active' ? 'inactive' : 'active',
            'updated_by' => Auth::id(),
        ]);

        return back();
    }

    public function reorder(Request $request)
    {
        $request->validate(['items' => 'required|array']);
        foreach ($request->items as $item) {
            HeroBanner::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }
        return back();
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function validated(Request $request, ?HeroBanner $existing = null): array
    {
        $mediaRequired = $existing ? 'nullable' : 'nullable';

        return $request->validate([
            'badge_text'         => 'nullable|string|max:100',
            'headline'           => 'required|string|max:300',
            'headline_highlight' => 'nullable|string|max:150',
            'subtext'            => 'nullable|string|max:1000',
            'cta_text'           => 'nullable|string|max:100',
            'cta_url'            => 'nullable|string|max:500',
            'stats'              => 'nullable|array',
            'stats.*.label'      => 'required|string|max:50',
            'stats.*.value'      => 'required|string|max:20',
            'stats.*.suffix'     => 'nullable|string|max:10',
            'media_type'         => 'required|in:none,video,image_full,image_side',
            'overlay_opacity'    => 'required|integer|min:0|max:100',
            'overlay_color'      => 'required|string|max:7',
            'image_position'     => 'required|in:right,left',
            'image_size'         => 'required|in:sm,md,lg',
            'image_align_y'      => 'required|in:top,center,bottom',
            'bg_type'            => 'required|in:gradient,solid',
            'bg_color_from'      => 'required|string|max:7',
            'bg_color_to'        => 'required|string|max:7',
            'text_color'         => 'required|in:dark,light',
            'animation_style'    => 'required|in:slide_up,typewriter,fade',
            'status'             => 'required|in:active,inactive',
        ]);
    }

    private function uploadMedia(Request $request, ?HeroBanner $existing = null): array
    {
        $request->validate([
            'media_file' => 'nullable|file|max:30720|mimetypes:video/mp4,video/quicktime,image/jpeg,image/png,image/webp,image/gif',
        ]);

        if (! $request->hasFile('media_file')) {
            return [
                'media_path' => $existing?->media_path,
                'media_url'  => $existing?->media_url,
            ];
        }

        if ($existing?->media_path) {
            $this->storage->delete($existing->media_path);
        }

        $file      = $request->file('media_file');
        $ext       = $file->getClientOriginalExtension();
        $path      = 'hero/' . uniqid('banner_') . '.' . $ext;
        $uploaded  = $this->storage->upload($file, $path);

        return [
            'media_path' => $uploaded['path'],
            'media_url'  => $uploaded['url'],
        ];
    }
}
