<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with(['features' => fn($q) => $q->orderBy('sort_order'), 'createdBy:id,name', 'updatedBy:id,name'])
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Admin/Packages/Index', ['packages' => $packages]);
    }

    public function create()
    {
        return Inertia::render('Admin/Packages/Form', ['package' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validate($request);
        $features = $request->input('features', []);

        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $package = Package::create($data);

        $this->syncFeatures($package, $features);

        return redirect()->route('admin.packages')->with('success', 'สร้างแพ็กเกจสำเร็จ');
    }

    public function edit(Package $package)
    {
        return Inertia::render('Admin/Packages/Form', [
            'package' => $package->load(['features' => fn($q) => $q->orderBy('sort_order')]),
        ]);
    }

    public function update(Request $request, Package $package)
    {
        $data = $this->validate($request);
        $features = $request->input('features', []);

        $data['updated_by'] = Auth::id();
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $package->update($data);

        $this->syncFeatures($package, $features);

        return redirect()->route('admin.packages')->with('success', 'อัปเดตแพ็กเกจสำเร็จ');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return back();
    }

    public function toggleStatus(Package $package)
    {
        $package->update([
            'status'     => $package->status === 'active' ? 'inactive' : 'active',
            'updated_by' => Auth::id(),
        ]);
        return back();
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items'              => 'required|array',
            'items.*.id'         => 'required|exists:packages,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            Package::where('id', $item['id'])->update([
                'sort_order' => $item['sort_order'],
                'updated_by' => Auth::id(),
            ]);
        }

        return back();
    }

    private function validate(Request $request): array
    {
        return $request->validate([
            'name'               => 'required|string|max:255',
            'slug'               => 'nullable|string|max:100',
            'tag_text'           => 'nullable|string|max:255',
            'page_structure'     => 'nullable|string|max:255',
            'price'              => 'required|integer|min:0',
            'original_price'     => 'nullable|integer|min:0',
            'is_vat_excluded'    => 'boolean',
            'description'        => 'nullable|string',
            'color_from'         => 'nullable|string|max:30',
            'color_to'           => 'nullable|string|max:30',
            'badge_text'         => 'nullable|string|max:50',
            'badge_color'        => 'nullable|string|max:30',
            'cta_primary_text'   => 'nullable|string|max:100',
            'cta_primary_url'    => 'nullable|string|max:255',
            'cta_secondary_text' => 'nullable|string|max:100',
            'cta_secondary_url'  => 'nullable|string|max:255',
            'portfolio_url'      => 'nullable|string|max:255',
            'detail_url'         => 'nullable|string|max:255',
            'meta_title'         => 'nullable|string|max:255',
            'meta_description'   => 'nullable|string',
            'status'             => 'required|in:active,inactive',
            'sort_order'         => 'required|integer|min:0',
        ]);
    }

    private function syncFeatures(Package $package, array $features): void
    {
        $keepIds = [];

        foreach ($features as $index => $f) {
            if (!empty($f['id'])) {
                PackageFeature::where('id', $f['id'])->where('package_id', $package->id)->update([
                    'title'          => $f['title'],
                    'description'    => $f['description'] ?? null,
                    'type'           => $f['type'] ?? 'included',
                    'is_highlighted' => $f['is_highlighted'] ?? false,
                    'sort_order'     => $index,
                    'updated_by'     => Auth::id(),
                ]);
                $keepIds[] = $f['id'];
            } else {
                $feature = $package->features()->create([
                    'title'          => $f['title'],
                    'description'    => $f['description'] ?? null,
                    'type'           => $f['type'] ?? 'included',
                    'is_highlighted' => $f['is_highlighted'] ?? false,
                    'sort_order'     => $index,
                    'created_by'     => Auth::id(),
                    'updated_by'     => Auth::id(),
                ]);
                $keepIds[] = $feature->id;
            }
        }

        $package->features()->whereNotIn('id', $keepIds)->delete();
    }
}
