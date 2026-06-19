<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogMediaController;
use App\Http\Controllers\Admin\HeroBannerController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\PackageController as AdminPackageController;
use App\Http\Controllers\Admin\PackageAddonController;
use App\Http\Controllers\Admin\PortfolioController as AdminPortfolioController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RolePermissionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ─── Public Routes ────────────────────────────────────────────────────────────

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio');

Route::prefix('services')->name('services.')->group(function () {
    Route::get('/', fn () => Inertia::render('Services/Index'))->name('index');
    Route::get('/website', [PackageController::class, 'index'])->name('website');
    Route::get('/google-ads', fn () => Inertia::render('Services/GoogleAds'))->name('google-ads');
});

Route::get('/faq', [FaqController::class, 'index'])->name('faq');

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
});

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/privacy-policy', fn () => Inertia::render('PrivacyPolicy'))->name('privacy-policy');

// ─── Admin Routes ─────────────────────────────────────────────────────────────

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {

    Route::get('/', fn () => Inertia::render('Admin/Dashboard'))->name('dashboard');

    // Site Settings
    Route::middleware('admin.permission:settings')->group(function () {
        Route::get('/settings', [SiteSettingController::class, 'index'])->name('settings');
        Route::post('/settings/general', [SiteSettingController::class, 'saveGeneral'])->name('settings.general');
        Route::post('/settings/contact', [SiteSettingController::class, 'saveContact'])->name('settings.contact');
        Route::post('/settings/social', [SiteSettingController::class, 'storeSocial'])->name('settings.social.store');
        Route::post('/settings/social/{socialLink}', [SiteSettingController::class, 'updateSocial'])->name('settings.social.update');
        Route::delete('/settings/social/{socialLink}', [SiteSettingController::class, 'destroySocial'])->name('settings.social.destroy');
        Route::post('/settings/social/reorder', [SiteSettingController::class, 'reorderSocial'])->name('settings.social.reorder');
    });

    // Hero Banner
    Route::middleware('admin.permission:hero_banner')->group(function () {
        Route::get('/hero-banner', [HeroBannerController::class, 'index'])->name('hero-banner');
        Route::get('/hero-banner/create', [HeroBannerController::class, 'create'])->name('hero-banner.create');
        Route::post('/hero-banner', [HeroBannerController::class, 'store'])->name('hero-banner.store');
        Route::get('/hero-banner/{heroBanner}/edit', [HeroBannerController::class, 'edit'])->name('hero-banner.edit');
        Route::post('/hero-banner/{heroBanner}', [HeroBannerController::class, 'update'])->name('hero-banner.update');
        Route::delete('/hero-banner/{heroBanner}', [HeroBannerController::class, 'destroy'])->name('hero-banner.destroy');
        Route::patch('/hero-banner/{heroBanner}/toggle', [HeroBannerController::class, 'toggleStatus'])->name('hero-banner.toggle');
        Route::post('/hero-banner/reorder', [HeroBannerController::class, 'reorder'])->name('hero-banner.reorder');
    });

    // Portfolio
    Route::middleware('admin.permission:portfolio')->group(function () {
        Route::get('/portfolio', [AdminPortfolioController::class, 'index'])->name('portfolio');
        Route::get('/portfolio/create', [AdminPortfolioController::class, 'create'])->name('portfolio.create');
        Route::post('/portfolio', [AdminPortfolioController::class, 'store'])->name('portfolio.store');
        Route::get('/portfolio/{portfolio}/edit', [AdminPortfolioController::class, 'edit'])->name('portfolio.edit');
        Route::post('/portfolio/{portfolio}', [AdminPortfolioController::class, 'update'])->name('portfolio.update');
        Route::delete('/portfolio/{portfolio}', [AdminPortfolioController::class, 'destroy'])->name('portfolio.destroy');
        Route::patch('/portfolio/{portfolio}/toggle', [AdminPortfolioController::class, 'toggleStatus'])->name('portfolio.toggle');
        Route::post('/portfolio/reorder', [AdminPortfolioController::class, 'reorder'])->name('portfolio.reorder');
    });

    // Blog
    Route::middleware('admin.permission:blog')->group(function () {
        Route::get('/blog', [AdminBlogController::class, 'index'])->name('blog');
        Route::get('/blog/categories', [BlogCategoryController::class, 'index'])->name('blog.categories');
        Route::post('/blog/categories', [BlogCategoryController::class, 'store'])->name('blog.categories.store');
        Route::put('/blog/categories/{blogCategory}', [BlogCategoryController::class, 'update'])->name('blog.categories.update');
        Route::delete('/blog/categories/{blogCategory}', [BlogCategoryController::class, 'destroy'])->name('blog.categories.destroy');
        Route::post('/blog/media/upload', [BlogMediaController::class, 'upload'])->name('blog.media.upload');
        Route::get('/blog/create', [AdminBlogController::class, 'create'])->name('blog.create');
        Route::post('/blog', [AdminBlogController::class, 'store'])->name('blog.store');
        Route::get('/blog/{blog}/edit', [AdminBlogController::class, 'edit'])->name('blog.edit');
        Route::post('/blog/{blog}', [AdminBlogController::class, 'update'])->name('blog.update');
        Route::delete('/blog/{blog}', [AdminBlogController::class, 'destroy'])->name('blog.destroy');
        Route::patch('/blog/{blog}/featured', [AdminBlogController::class, 'toggleFeatured'])->name('blog.featured');
        Route::patch('/blog/{blog}/status', [AdminBlogController::class, 'updateStatus'])->name('blog.status');
    });

    // Packages
    Route::middleware('admin.permission:packages')->group(function () {
        Route::get('/packages', [AdminPackageController::class, 'index'])->name('packages');
        Route::get('/packages/create', [AdminPackageController::class, 'create'])->name('packages.create');
        Route::post('/packages', [AdminPackageController::class, 'store'])->name('packages.store');
        Route::get('/packages/{package}/edit', [AdminPackageController::class, 'edit'])->name('packages.edit');
        Route::put('/packages/{package}', [AdminPackageController::class, 'update'])->name('packages.update');
        Route::delete('/packages/{package}', [AdminPackageController::class, 'destroy'])->name('packages.destroy');
        Route::patch('/packages/{package}/toggle', [AdminPackageController::class, 'toggleStatus'])->name('packages.toggle');
        Route::post('/packages/reorder', [AdminPackageController::class, 'reorder'])->name('packages.reorder');

        Route::get('/packages/addons', [PackageAddonController::class, 'index'])->name('packages.addons');
        Route::post('/packages/addons', [PackageAddonController::class, 'store'])->name('packages.addons.store');
        Route::put('/packages/addons/{addon}', [PackageAddonController::class, 'update'])->name('packages.addons.update');
        Route::delete('/packages/addons/{addon}', [PackageAddonController::class, 'destroy'])->name('packages.addons.destroy');
        Route::patch('/packages/addons/{addon}/toggle', [PackageAddonController::class, 'toggleStatus'])->name('packages.addons.toggle');
        Route::post('/packages/addons/reorder', [PackageAddonController::class, 'reorder'])->name('packages.addons.reorder');
    });

    // Partners
    Route::middleware('admin.permission:partners')->group(function () {
        Route::get('/partners', [PartnerController::class, 'index'])->name('partners');
        Route::post('/partners', [PartnerController::class, 'store'])->name('partners.store');
        Route::post('/partners/{partner}', [PartnerController::class, 'update'])->name('partners.update');
        Route::delete('/partners/{partner}', [PartnerController::class, 'destroy'])->name('partners.destroy');
        Route::patch('/partners/{partner}/toggle', [PartnerController::class, 'toggleStatus'])->name('partners.toggle');
        Route::post('/partners/reorder', [PartnerController::class, 'reorder'])->name('partners.reorder');
        Route::post('/partners/settings', [PartnerController::class, 'saveSettings'])->name('partners.settings');
    });

    // Testimonials
    Route::middleware('admin.permission:testimonials')->group(function () {
        Route::get('/testimonials', [TestimonialController::class, 'index'])->name('testimonials');
        Route::post('/testimonials', [TestimonialController::class, 'store'])->name('testimonials.store');
        Route::post('/testimonials/{testimonial}', [TestimonialController::class, 'update'])->name('testimonials.update');
        Route::delete('/testimonials/{testimonial}', [TestimonialController::class, 'destroy'])->name('testimonials.destroy');
        Route::patch('/testimonials/{testimonial}/toggle', [TestimonialController::class, 'toggleStatus'])->name('testimonials.toggle');
        Route::post('/testimonials/reorder', [TestimonialController::class, 'reorder'])->name('testimonials.reorder');
        Route::post('/testimonials/settings', [TestimonialController::class, 'saveSettings'])->name('testimonials.settings');
    });

    // Contacts
    Route::middleware('admin.permission:contacts')->group(function () {
        Route::get('/contacts', [AdminContactController::class, 'index'])->name('contacts');
        Route::get('/contacts/{contact}', [AdminContactController::class, 'show'])->name('contacts.show');
        Route::patch('/contacts/{contact}/status', [AdminContactController::class, 'updateStatus'])->name('contacts.status');
        Route::delete('/contacts/{contact}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');
    });

    // Services
    Route::middleware('admin.permission:services')->group(function () {
        Route::get('/services', [ServiceController::class, 'index'])->name('services');
        Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
        Route::post('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
        Route::patch('/services/{service}/toggle', [ServiceController::class, 'toggleStatus'])->name('services.toggle');
        Route::post('/services/reorder', [ServiceController::class, 'reorder'])->name('services.reorder');
    });

    // FAQ
    Route::middleware('admin.permission:faq')->group(function () {
        Route::get('/faq', [AdminFaqController::class, 'index'])->name('faq');
        Route::post('/faq', [AdminFaqController::class, 'store'])->name('faq.store');
        Route::put('/faq/{faq}', [AdminFaqController::class, 'update'])->name('faq.update');
        Route::delete('/faq/{faq}', [AdminFaqController::class, 'destroy'])->name('faq.destroy');
        Route::patch('/faq/{faq}/toggle', [AdminFaqController::class, 'toggleStatus'])->name('faq.toggle');
        Route::post('/faq/reorder', [AdminFaqController::class, 'reorder'])->name('faq.reorder');
    });

    // Users (super_admin / admin.permission:users)
    Route::middleware('admin.permission:users')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::post('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Roles (super_admin / admin.permission:roles)
    Route::middleware('admin.permission:roles')->group(function () {
        Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles');
        Route::post('/roles', [RolePermissionController::class, 'store'])->name('roles.store');
        Route::post('/roles/{role}', [RolePermissionController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RolePermissionController::class, 'destroy'])->name('roles.destroy');
    });
});

// ─── Auth & Profile Routes ────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
