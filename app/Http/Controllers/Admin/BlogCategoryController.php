<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BlogCategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Blog/Categories', [
            'categories' => BlogCategory::withCount('blogs')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'color'       => 'required|string|max:7',
        ]);

        $slug = $this->uniqueSlug(Str::slug($data['name']));

        BlogCategory::create(array_merge($data, [
            'slug'       => $slug,
            'sort_order' => BlogCategory::max('sort_order') + 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]));

        return back()->with('success', 'เพิ่มหมวดหมู่เรียบร้อยแล้ว');
    }

    public function update(Request $request, BlogCategory $blogCategory)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'color'       => 'required|string|max:7',
        ]);

        $blogCategory->update(array_merge($data, ['updated_by' => Auth::id()]));

        return back()->with('success', 'อัปเดตหมวดหมู่เรียบร้อยแล้ว');
    }

    public function destroy(BlogCategory $blogCategory)
    {
        $blogCategory->delete();
        return back()->with('success', 'ลบหมวดหมู่เรียบร้อยแล้ว');
    }

    private function uniqueSlug(string $base): string
    {
        $slug = $base ?: 'category';
        $n    = 0;
        while (BlogCategory::where('slug', $n ? "{$slug}-{$n}" : $slug)->exists()) {
            $n++;
        }
        return $n ? "{$slug}-{$n}" : $slug;
    }
}
