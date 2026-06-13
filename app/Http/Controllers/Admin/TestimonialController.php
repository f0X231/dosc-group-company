<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\TestimonialSetting;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TestimonialController extends Controller
{
    public function __construct(private SupabaseStorageService $storage) {}

    public function index()
    {
        return Inertia::render('Admin/Testimonial/Index', [
            'testimonials' => Testimonial::orderBy('sort_order')->orderBy('id')->get(),
            'settings'     => TestimonialSetting::current(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $testimonial = Testimonial::create(array_merge($data, [
            'sort_order'  => Testimonial::max('sort_order') + 1,
            'created_by'  => Auth::id(),
            'updated_by'  => Auth::id(),
        ]));

        $this->uploadAvatar($request, $testimonial);

        return back()->with('success', 'เพิ่มรีวิวเรียบร้อยแล้ว');
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $this->validated($request);

        $testimonial->update(array_merge($data, ['updated_by' => Auth::id()]));

        $this->uploadAvatar($request, $testimonial);

        return back()->with('success', 'อัปเดตรีวิวเรียบร้อยแล้ว');
    }

    public function destroy(Testimonial $testimonial)
    {
        if ($testimonial->avatar_path) {
            $this->storage->delete($testimonial->avatar_path);
        }
        $testimonial->delete();

        return back()->with('success', 'ลบรีวิวเรียบร้อยแล้ว');
    }

    public function toggleStatus(Testimonial $testimonial)
    {
        $testimonial->update([
            'status'     => $testimonial->status === 'active' ? 'inactive' : 'active',
            'updated_by' => Auth::id(),
        ]);
        return back();
    }

    public function reorder(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        foreach ($request->ids as $order => $id) {
            Testimonial::where('id', $id)->update(['sort_order' => $order]);
        }

        return response()->json(['ok' => true]);
    }

    public function saveSettings(Request $request)
    {
        $data = $request->validate([
            'label'        => 'required|string|max:50',
            'heading'      => 'required|string|max:200',
            'bg_color'     => 'required|string|max:7',
            'overlay_text' => 'nullable|string|max:150',
            'cta_text'     => 'nullable|string|max:50',
            'cta_url'      => 'nullable|string|max:500',
        ]);

        $settings = TestimonialSetting::current();
        $settings->update($data);

        if ($request->hasFile('featured_image')) {
            $request->validate(['featured_image' => 'file|image|max:5120']);
            if ($settings->featured_image_path) {
                $this->storage->delete($settings->featured_image_path);
            }
            $file     = $request->file('featured_image');
            $path     = 'testimonials/featured.' . $file->getClientOriginalExtension();
            $uploaded = $this->storage->upload($file, $path);
            $settings->update([
                'featured_image_path' => $uploaded['path'],
                'featured_image_url'  => $uploaded['url'],
            ]);
        }

        return back()->with('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function validated(Request $request): array
    {
        return $request->validate([
            'customer_name'  => 'required|string|max:100',
            'customer_title' => 'nullable|string|max:150',
            'avatar_color'   => 'nullable|string|max:7',
            'review_text'    => 'required|string',
            'rating'         => 'required|integer|min:1|max:5',
            'source'         => 'required|in:direct,google,facebook',
            'source_url'     => 'nullable|string|max:500',
            'status'         => 'required|in:active,inactive',
        ]);
    }

    private function uploadAvatar(Request $request, Testimonial $testimonial): void
    {
        if (! $request->hasFile('avatar')) return;

        $request->validate(['avatar' => 'file|image|max:2048']);

        if ($testimonial->avatar_path) {
            $this->storage->delete($testimonial->avatar_path);
        }
        $file     = $request->file('avatar');
        $path     = "testimonials/avatars/{$testimonial->id}.{$file->getClientOriginalExtension()}";
        $uploaded = $this->storage->upload($file, $path);
        $testimonial->update([
            'avatar_path' => $uploaded['path'],
            'avatar_url'  => $uploaded['url'],
        ]);
    }
}
