<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Inertia\Inertia;

class PortfolioController extends Controller
{
    public function index()
    {
        $portfolios = Portfolio::active()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get([
                'id', 'title', 'slug', 'package_name', 'client_url',
                'video_url', 'thumbnail_url', 'description',
            ]);

        $tags = $portfolios
            ->pluck('package_name')
            ->filter()
            ->unique()
            ->values();

        return Inertia::render('Portfolio/Index', [
            'portfolios' => $portfolios,
            'tags'       => $tags,
        ]);
    }
}
