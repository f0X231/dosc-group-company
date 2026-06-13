<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function __construct(private SupabaseStorageService $storage) {}

    public function index()
    {
        return Inertia::render('Admin/Service/Index', [
            'services' => Service::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $service = Service::create(array_merge($data, [
            'sort_order' => Service::max('sort_order') + 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]));

        $this->uploadImage($request, $service);

        return back()->with('success', 'เพิ่มบริการเรียบร้อยแล้ว');
    }

    public function update(Request $request, Service $service)
    {
        $data = $this->validated($request);
        $service->update(array_merge($data, ['updated_by' => Auth::id()]));
        $this->uploadImage($request, $service);

        return back()->with('success', 'อัปเดตบริการเรียบร้อยแล้ว');
    }

    public function destroy(Service $service)
    {
        if ($service->image_path) {
            $this->storage->delete($service->image_path);
        }
        $service->delete();

        return back()->with('success', 'ลบบริการเรียบร้อยแล้ว');
    }

    public function toggleStatus(Service $service)
    {
        $service->update([
            'status'     => $service->status === 'active' ? 'inactive' : 'active',
            'updated_by' => Auth::id(),
        ]);
        return back();
    }

    public function reorder(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        foreach ($request->ids as $order => $id) {
            Service::where('id', $id)->update(['sort_order' => $order]);
        }

        return response()->json(['ok' => true]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'       => 'required|string|max:200',
            'subtitle'    => 'nullable|string|max:200',
            'description' => 'nullable|string|max:1000',
            'icon_name'   => 'nullable|string|max:50',
            'cta_text'    => 'nullable|string|max:100',
            'cta_url'     => 'nullable|string|max:500',
            'badge_text'  => 'nullable|string|max:50',
            'badge_color' => 'nullable|string|max:30',
            'status'      => 'required|in:active,inactive',
        ]);
    }

    private function uploadImage(Request $request, Service $service): void
    {
        if (! $request->hasFile('image')) return;

        $request->validate(['image' => 'file|mimes:jpeg,jpg,png,webp|max:4096']);

        if ($service->image_path) {
            $this->storage->delete($service->image_path);
        }
        $file     = $request->file('image');
        $ext      = $file->getClientOriginalExtension();
        $path     = "services/{$service->id}.{$ext}";
        $uploaded = $this->storage->upload($file, $path);
        $service->update([
            'image_path' => $uploaded['path'],
            'image_url'  => $uploaded['url'],
        ]);
    }
}
