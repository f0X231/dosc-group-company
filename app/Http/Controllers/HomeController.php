<?php

namespace App\Http\Controllers;

use App\Models\HeroBanner;
use App\Models\Partner;
use App\Models\PartnerSetting;
use App\Models\Testimonial;
use App\Models\TestimonialSetting;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $hero = HeroBanner::where('status', 'active')
            ->orderBy('sort_order')
            ->first();

        $testimonialSettings = TestimonialSetting::current();
        $testimonials        = Testimonial::active()
            ->orderBy('sort_order')
            ->get(['id', 'customer_name', 'customer_title', 'avatar_url', 'avatar_color', 'review_text', 'rating', 'source', 'source_url']);

        $partnerSettings = PartnerSetting::current();
        $partners        = Partner::active()
            ->orderBy('sort_order')
            ->get(['id', 'name', 'logo_url', 'website_url']);

        return Inertia::render('Home', [
            'hero'                => $hero,
            'testimonialSettings' => $testimonialSettings,
            'testimonials'        => $testimonials,
            'partnerSettings'     => $partnerSettings,
            'partners'            => $partners,
        ]);
    }
}
