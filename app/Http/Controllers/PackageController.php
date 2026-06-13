<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackageAddon;
use App\Models\Portfolio;
use App\Models\WhyUsItem;
use App\Models\WorkStep;
use Inertia\Inertia;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::active()
            ->with(['features' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $addons = PackageAddon::active()->orderBy('sort_order')->get();

        $workSteps = WorkStep::orderBy('sort_order')->get();

        $whyUsItems = WhyUsItem::active()->orderBy('sort_order')->get();

        $portfolios = Portfolio::active()
            ->orderBy('sort_order')
            ->limit(6)
            ->get(['id', 'title', 'package_name', 'thumbnail_url', 'client_url']);

        return Inertia::render('Services/Website', [
            'packages'   => $packages,
            'addons'     => $addons,
            'workSteps'  => $workSteps,
            'whyUsItems' => $whyUsItems,
            'portfolios' => $portfolios,
        ]);
    }
}
