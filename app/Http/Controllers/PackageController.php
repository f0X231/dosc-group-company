<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackageAddon;
use Inertia\Inertia;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::active()
            ->with(['features' => fn($q) => $q->where('type', 'included')->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $addons = PackageAddon::active()->orderBy('sort_order')->get();

        return Inertia::render('Services/Website', [
            'packages' => $packages,
            'addons'   => $addons,
        ]);
    }
}
