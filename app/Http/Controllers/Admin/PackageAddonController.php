<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PackageAddon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PackageAddonController extends Controller
{
    public function index()
    {
        $addons = PackageAddon::with(['createdBy:id,name', 'updatedBy:id,name'])
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Admin/Packages/Addons', ['addons' => $addons]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|integer|min:0',
            'status'      => 'required|in:active,inactive',
            'sort_order'  => 'required|integer|min:0',
        ]);

        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        PackageAddon::create($data);

        return back();
    }

    public function update(Request $request, PackageAddon $addon)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|integer|min:0',
            'status'      => 'required|in:active,inactive',
            'sort_order'  => 'required|integer|min:0',
        ]);

        $data['updated_by'] = Auth::id();

        $addon->update($data);

        return back();
    }

    public function destroy(PackageAddon $addon)
    {
        $addon->delete();
        return back();
    }

    public function toggleStatus(PackageAddon $addon)
    {
        $addon->update([
            'status'     => $addon->status === 'active' ? 'inactive' : 'active',
            'updated_by' => Auth::id(),
        ]);
        return back();
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items'              => 'required|array',
            'items.*.id'         => 'required|exists:package_addons,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            PackageAddon::where('id', $item['id'])->update([
                'sort_order' => $item['sort_order'],
                'updated_by' => Auth::id(),
            ]);
        }

        return back();
    }
}
