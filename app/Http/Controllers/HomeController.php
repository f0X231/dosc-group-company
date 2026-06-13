<?php

namespace App\Http\Controllers;

use App\Models\HeroBanner;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $hero = HeroBanner::where('status', 'active')
            ->orderBy('sort_order')
            ->first();

        return Inertia::render('Home', [
            'hero' => $hero,
        ]);
    }
}
