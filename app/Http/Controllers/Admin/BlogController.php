<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogMedia;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function __construct(private SupabaseStorageService $storage) {}

    public function index(Request $request)
    {
        $query = Blog::with('category')
            ->withCount('media')
            ->orderByDesc('created_at');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return Inertia::render('Admin/Blog/Index', [
            'blogs'      => $query->paginate(20)->withQueryString(),
            'categories' => BlogCategory::orderBy('sort_order')->get(['id', 'name', 'color']),
            'filters'    => $request->only('status'),
            'counts'     => [
                'all'       => Blog::count(),
                'draft'     => Blog::where('status', 'draft')->count(),
                'published' => Blog::where('status', 'published')->count(),
                'scheduled' => Blog::where('status', 'scheduled')->count(),
                'archived'  => Blog::where('status', 'archived')->count(),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Blog/Form', [
            'blog'       => null,
            'categories' => BlogCategory::orderBy('sort_order')->get(['id', 'name', 'color']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $slug = $this->uniqueSlug(Str::slug($data['title']));

        $blog = Blog::create(array_merge($data, [
            'slug'        => $slug,
            'reading_time' => Blog::calcReadingTime($data['content'] ?? ''),
            'created_by'  => Auth::id(),
            'updated_by'  => Auth::id(),
        ]));

        // Link media uploaded during editing
        $this->linkMedia($request->input('blog_uuid'), $blog);

        // Upload cover + thumbnail
        $this->uploadImages($request, $blog);

        return redirect()->route('admin.blog')->with('success', 'สร้างบทความเรียบร้อยแล้ว');
    }

    public function edit(Blog $blog)
    {
        return Inertia::render('Admin/Blog/Form', [
            'blog'       => $blog->load('category', 'media'),
            'categories' => BlogCategory::orderBy('sort_order')->get(['id', 'name', 'color']),
        ]);
    }

    public function update(Request $request, Blog $blog)
    {
        $data = $this->validated($request, $blog);

        $blog->update(array_merge($data, [
            'reading_time' => Blog::calcReadingTime($data['content'] ?? ''),
            'updated_by'   => Auth::id(),
        ]));

        $this->uploadImages($request, $blog);

        return redirect()->route('admin.blog')->with('success', 'อัปเดตบทความเรียบร้อยแล้ว');
    }

    public function destroy(Blog $blog)
    {
        // Delete all associated media from storage
        foreach ($blog->media as $media) {
            $this->storage->delete($media->storage_path);
        }
        if ($blog->cover_image_path) {
            $this->storage->delete($blog->cover_image_path);
        }
        if ($blog->thumbnail_path) {
            $this->storage->delete($blog->thumbnail_path);
        }

        $blog->delete();

        return back()->with('success', 'ลบบทความเรียบร้อยแล้ว');
    }

    public function toggleFeatured(Blog $blog)
    {
        $blog->update(['is_featured' => ! $blog->is_featured, 'updated_by' => Auth::id()]);
        return back();
    }

    public function updateStatus(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'status'       => 'required|in:draft,scheduled,published,archived',
            'published_at' => 'nullable|date',
        ]);

        $blog->update(array_merge($data, ['updated_by' => Auth::id()]));

        return back()->with('success', 'อัปเดตสถานะเรียบร้อยแล้ว');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function validated(Request $request, ?Blog $existing = null): array
    {
        return $request->validate([
            'uuid'             => 'required|string|max:36',
            'title'            => 'required|string|max:300',
            'excerpt'          => 'nullable|string|max:500',
            'content'          => 'nullable|string',
            'category_id'      => 'nullable|exists:blog_categories,id',
            'tags'             => 'nullable|array',
            'tags.*'           => 'string|max:50',
            'status'           => 'required|in:draft,scheduled,published,archived',
            'published_at'     => 'nullable|date',
            'is_featured'      => 'boolean',
            'meta_title'       => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords'    => 'nullable|string|max:255',
            'canonical_url'    => 'nullable|string|max:500',
            'head_script'      => 'nullable|string|max:50000',
            'body_script'      => 'nullable|string|max:50000',
            'og_image_url'     => 'nullable|string|max:500',
        ]);
    }

    private function linkMedia(string $blogUuid, Blog $blog): void
    {
        BlogMedia::where('blog_uuid', $blogUuid)
            ->whereNull('blog_id')
            ->update(['blog_id' => $blog->id]);
    }

    private function uploadImages(Request $request, Blog $blog): void
    {
        $request->validate([
            'cover_image'  => 'nullable|file|image|max:5120',
            'thumbnail'    => 'nullable|file|image|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($blog->cover_image_path) {
                $this->storage->delete($blog->cover_image_path);
            }
            $file     = $request->file('cover_image');
            $path     = "articles/{$blog->uuid}/cover.{$file->getClientOriginalExtension()}";
            $uploaded = $this->storage->upload($file, $path);
            $blog->update(['cover_image_path' => $uploaded['path'], 'cover_image_url' => $uploaded['url']]);
        }

        if ($request->hasFile('thumbnail')) {
            if ($blog->thumbnail_path) {
                $this->storage->delete($blog->thumbnail_path);
            }
            $file     = $request->file('thumbnail');
            $path     = "articles/{$blog->uuid}/thumbnail.{$file->getClientOriginalExtension()}";
            $uploaded = $this->storage->upload($file, $path);
            $blog->update(['thumbnail_path' => $uploaded['path'], 'thumbnail_url' => $uploaded['url']]);
        }
    }

    private function uniqueSlug(string $base): string
    {
        $slug = $base ?: 'blog';
        $n    = 0;
        while (Blog::where('slug', $n ? "{$slug}-{$n}" : $slug)->exists()) {
            $n++;
        }
        return $n ? "{$slug}-{$n}" : $slug;
    }
}
