<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\PartnerSetting;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PartnerController extends Controller
{
    public function __construct(private SupabaseStorageService $storage) {}

    public function index()
    {
        return Inertia::render('Admin/Partner/Index', [
            'partners' => Partner::orderBy('sort_order')->orderBy('id')->get(),
            'settings' => PartnerSetting::current(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $partner = Partner::create(array_merge($data, [
            'sort_order' => Partner::max('sort_order') + 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]));

        $this->uploadLogo($request, $partner);

        return back()->with('success', 'เพิ่ม partner เรียบร้อยแล้ว');
    }

    public function update(Request $request, Partner $partner)
    {
        $data = $this->validated($request);
        $partner->update(array_merge($data, ['updated_by' => Auth::id()]));
        $this->uploadLogo($request, $partner);

        return back()->with('success', 'อัปเดต partner เรียบร้อยแล้ว');
    }

    public function destroy(Partner $partner)
    {
        if ($partner->logo_path) {
            $this->storage->delete($partner->logo_path);
        }
        $partner->delete();

        return back()->with('success', 'ลบ partner เรียบร้อยแล้ว');
    }

    public function toggleStatus(Partner $partner)
    {
        $partner->update([
            'status'     => $partner->status === 'active' ? 'inactive' : 'active',
            'updated_by' => Auth::id(),
        ]);
        return back();
    }

    public function reorder(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        foreach ($request->ids as $order => $id) {
            Partner::where('id', $id)->update(['sort_order' => $order]);
        }

        return response()->json(['ok' => true]);
    }

    public function saveSettings(Request $request)
    {
        $data = $request->validate([
            'heading'       => 'required|string|max:200',
            'bg_color'      => 'required|string|max:7',
            'logos_per_row' => 'required|integer|min:2|max:6',
            'show_name'     => 'boolean',
            'grayscale'     => 'boolean',
        ]);

        PartnerSetting::current()->update($data);

        return back()->with('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'        => 'required|string|max:100',
            'website_url' => 'nullable|string|max:500',
            'status'      => 'required|in:active,inactive',
        ]);
    }

    private function uploadLogo(Request $request, Partner $partner): void
    {
        if (! $request->hasFile('logo')) return;

        $request->validate(['logo' => 'file|mimes:jpeg,jpg,png,gif,webp,svg|max:2048']);

        if ($partner->logo_path) {
            $this->storage->delete($partner->logo_path);
        }
        $file     = $request->file('logo');
        $ext      = $file->getClientOriginalExtension();
        $path     = "partners/{$partner->id}.{$ext}";
        $uploaded = $this->storage->upload($file, $path);
        $partner->update([
            'logo_path' => $uploaded['path'],
            'logo_url'  => $uploaded['url'],
        ]);
    }
}
