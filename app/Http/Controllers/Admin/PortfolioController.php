<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Portfolio;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PortfolioController extends Controller
{
    public function __construct(private SupabaseStorageService $storage) {}

    public function index()
    {
        $portfolios = Portfolio::orderBy('sort_order')->orderByDesc('id')->get();

        return Inertia::render('Admin/Portfolio/Index', [
            'portfolios' => $portfolios,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Portfolio/Form', [
            'portfolio' => null,
            'packages'  => Package::active()->orderBy('sort_order')->get(['id', 'name', 'tag_text']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'package_id'  => 'nullable|exists:packages,id',
            'client_url'  => 'nullable|url|max:500',
            'description' => 'nullable|string|max:2000',
            'status'      => 'required|in:active,inactive',
            'video'       => 'required|file|mimetypes:video/mp4,video/quicktime|max:6144',
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $slug = $this->uniqueSlug(Str::slug($data['title']));

        $video     = $this->uploadVideo($request, $slug);
        $thumbnail = $this->uploadThumbnail($request, $slug);

        $package      = $data['package_id'] ? Package::find($data['package_id']) : null;
        $packageName  = $package?->tag_text ?? $package?->name;

        Portfolio::create([
            'title'          => $data['title'],
            'slug'           => $slug,
            'package_id'     => $data['package_id'] ?? null,
            'package_name'   => $packageName,
            'client_url'     => $data['client_url'] ?? null,
            'video_path'     => $video['path'],
            'video_url'      => $video['url'],
            'thumbnail_path' => $thumbnail['path'] ?? null,
            'thumbnail_url'  => $thumbnail['url'] ?? null,
            'file_size'      => $request->file('video')?->getSize(),
            'description'    => $data['description'] ?? null,
            'status'         => $data['status'],
            'sort_order'     => Portfolio::max('sort_order') + 1,
            'created_by'     => Auth::id(),
            'updated_by'     => Auth::id(),
        ]);

        return redirect()->route('admin.portfolio')->with('success', 'เพิ่มผลงานเรียบร้อยแล้ว');
    }

    public function edit(Portfolio $portfolio)
    {
        return Inertia::render('Admin/Portfolio/Form', [
            'portfolio' => $portfolio,
            'packages'  => Package::active()->orderBy('sort_order')->get(['id', 'name', 'tag_text']),
        ]);
    }

    public function update(Request $request, Portfolio $portfolio)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'package_id'  => 'nullable|exists:packages,id',
            'client_url'  => 'nullable|url|max:500',
            'description' => 'nullable|string|max:2000',
            'status'      => 'required|in:active,inactive',
            'video'       => 'nullable|file|mimetypes:video/mp4,video/quicktime|max:6144',
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $updates = [
            'title'       => $data['title'],
            'package_id'  => $data['package_id'] ?? null,
            'client_url'  => $data['client_url'] ?? null,
            'description' => $data['description'] ?? null,
            'status'      => $data['status'],
            'updated_by'  => Auth::id(),
        ];

        $package = $data['package_id'] ? Package::find($data['package_id']) : null;
        $updates['package_name'] = $package?->tag_text ?? $package?->name;

        if ($request->hasFile('video')) {
            if ($portfolio->video_path) {
                $this->storage->delete($portfolio->video_path);
            }
            $video = $this->uploadVideo($request, $portfolio->slug);
            $updates['video_path'] = $video['path'];
            $updates['video_url']  = $video['url'];
            $updates['file_size']  = $request->file('video')->getSize();
        }

        if ($request->hasFile('thumbnail')) {
            if ($portfolio->thumbnail_path) {
                $this->storage->delete($portfolio->thumbnail_path);
            }
            $thumbnail = $this->uploadThumbnail($request, $portfolio->slug);
            $updates['thumbnail_path'] = $thumbnail['path'];
            $updates['thumbnail_url']  = $thumbnail['url'];
        }

        $portfolio->update($updates);

        return redirect()->route('admin.portfolio')->with('success', 'อัปเดตผลงานเรียบร้อยแล้ว');
    }

    public function destroy(Portfolio $portfolio)
    {
        $paths = array_filter([
            $portfolio->video_path,
            $portfolio->thumbnail_path,
        ]);

        if ($paths) {
            $this->storage->delete(array_values($paths));
        }

        $portfolio->delete();

        return redirect()->route('admin.portfolio')->with('success', 'ลบผลงานเรียบร้อยแล้ว');
    }

    public function toggleStatus(Portfolio $portfolio)
    {
        $portfolio->update([
            'status'     => $portfolio->status === 'active' ? 'inactive' : 'active',
            'updated_by' => Auth::id(),
        ]);

        return back();
    }

    public function reorder(Request $request)
    {
        $request->validate(['items' => 'required|array']);

        foreach ($request->items as $item) {
            Portfolio::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return back();
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function uploadVideo(Request $request, string $slug): array
    {
        $file = $request->file('video');
        $path = "videos/{$slug}-" . time() . '.mp4';

        return $this->storage->upload($file, $path);
    }

    private function uploadThumbnail(Request $request, string $slug): ?array
    {
        if (! $request->hasFile('thumbnail')) {
            return null;
        }

        $file      = $request->file('thumbnail');
        $extension = $file->getClientOriginalExtension();
        $path      = "thumbnails/{$slug}-" . time() . ".{$extension}";

        return $this->storage->upload($file, $path);
    }

    private function uniqueSlug(string $base): string
    {
        $slug  = $base ?: 'portfolio';
        $count = 0;

        while (Portfolio::where('slug', $count ? "{$slug}-{$count}" : $slug)->exists()) {
            $count++;
        }

        return $count ? "{$slug}-{$count}" : $slug;
    }
}
