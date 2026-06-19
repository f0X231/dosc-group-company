<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Faq;
use App\Models\HeroBanner;
use App\Models\Package;
use App\Models\Partner;
use App\Models\PartnerSetting;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\TestimonialSetting;
use App\Models\WhyUsItem;
use App\Models\WorkStep;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $hero = HeroBanner::where('status', 'active')
            ->orderBy('sort_order')
            ->first();

        $services = Service::active()
            ->orderBy('sort_order')
            ->get(['id', 'title', 'subtitle', 'description', 'icon_name', 'image_url', 'cta_text', 'cta_url', 'badge_text', 'badge_color', 'card_bg_color']);

        $portfolios = Portfolio::active()
            ->orderBy('sort_order')
            ->get(['id', 'title', 'package_name', 'thumbnail_url', 'client_url']);

        $packages = Package::active()
            ->orderBy('sort_order')
            ->with(['features' => fn ($q) => $q->where('type', 'included')->orderBy('sort_order')])
            ->get();

        $workSteps = WorkStep::orderBy('sort_order')->get();

        $whyUsItems = WhyUsItem::active()->orderBy('sort_order')->get();

        $testimonialSettings = TestimonialSetting::current();
        $testimonials        = Testimonial::active()
            ->orderBy('sort_order')
            ->get(['id', 'customer_name', 'customer_title', 'avatar_url', 'avatar_color', 'review_text', 'rating', 'source', 'source_url']);

        $partnerSettings = PartnerSetting::current();
        $partners        = Partner::active()
            ->orderBy('sort_order')
            ->get(['id', 'name', 'logo_url', 'website_url']);

        $faqs = Faq::active()->orderBy('sort_order')->get(['id', 'question', 'answer']);

        $latestBlogs = Blog::where('status', 'published')
            ->latest('published_at')
            ->limit(3)
            ->get(['id', 'title', 'slug', 'excerpt', 'cover_image_url', 'published_at']);

        return Inertia::render('Home', [
            'hero'                => $hero,
            'services'            => $services,
            'portfolios'          => $portfolios,
            'packages'            => $packages,
            'workSteps'           => $workSteps,
            'whyUsItems'          => $whyUsItems,
            'testimonialSettings' => $testimonialSettings,
            'testimonials'        => $testimonials,
            'partnerSettings'     => $partnerSettings,
            'partners'            => $partners,
            'faqs'                => $faqs,
            'latestBlogs'         => $latestBlogs,
        ]);
    }
}
