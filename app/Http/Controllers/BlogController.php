<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::published()->with('category')
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at');

        if ($category = $request->query('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $category));
        }

        return Inertia::render('Blog/Index', [
            'blogs'      => $query->paginate(12)->withQueryString(),
            'categories' => BlogCategory::orderBy('sort_order')->get(['id', 'name', 'slug', 'color']),
            'filters'    => $request->only('category'),
        ]);
    }

    public function show(string $slug)
    {
        $blog = Blog::published()
            ->with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment view count
        $blog->increment('view_count');

        $related = Blog::published()
            ->with('category')
            ->where('id', '!=', $blog->id)
            ->where('category_id', $blog->category_id)
            ->latest('published_at')
            ->take(3)
            ->get(['id', 'title', 'slug', 'excerpt', 'thumbnail_url', 'cover_image_url', 'published_at', 'reading_time']);

        return Inertia::render('Blog/Show', [
            'blog'    => $blog,
            'related' => $related,
            'seo'     => $this->buildBlogSeo($blog),
        ]);
    }

    private function buildBlogSeo(Blog $blog): array
    {
        $siteName   = config('app.name', 'DOSC Group');
        $title      = $blog->meta_title ?: "{$blog->title} | {$siteName}";
        $excerpt    = $blog->excerpt ? Str::limit(strip_tags($blog->excerpt), 155) : null;
        $desc       = $blog->meta_description ?: $excerpt;
        $ogImage    = $blog->og_image_url ?: $blog->cover_image_url ?: $blog->thumbnail_url;

        $schema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'Article',
            'headline'        => $blog->title,
            'description'     => $desc,
            'datePublished'   => $blog->published_at?->toIso8601String(),
            'dateModified'    => $blog->updated_at->toIso8601String(),
            'author'          => ['@type' => 'Organization', 'name' => $siteName],
            'publisher'       => ['@type' => 'Organization', 'name' => $siteName],
        ];
        if ($ogImage) $schema['image'] = $ogImage;

        return [
            'meta_title'       => $title,
            'meta_description' => $desc,
            'og_title'         => $blog->meta_title ?: $blog->title,
            'og_description'   => $desc,
            'og_image_url'     => $ogImage,
            'robots'           => 'index,follow',
            'canonical_url'    => $blog->canonical_url,
            'schema_json'      => json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];
    }
}
