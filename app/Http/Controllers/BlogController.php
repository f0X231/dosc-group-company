<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
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
        ]);
    }
}
