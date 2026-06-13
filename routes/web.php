<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\PackageController as AdminPackageController;
use App\Http\Controllers\Admin\PackageAddonController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ─── Public Routes ────────────────────────────────────────────────────────────

Route::get('/', fn () => Inertia::render('Home'))->name('home');

Route::get('/portfolio', fn () => Inertia::render('Portfolio/Index'))->name('portfolio');

Route::prefix('services')->name('services.')->group(function () {
    Route::get('/', fn () => Inertia::render('Services/Index'))->name('index');
    Route::get('/website', [PackageController::class, 'index'])->name('website');
    Route::get('/google-ads', fn () => Inertia::render('Services/GoogleAds'))->name('google-ads');
});

Route::get('/faq', [FaqController::class, 'index'])->name('faq');

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', fn () => Inertia::render('Blog/Index'))->name('index');
    Route::get('/{slug}', fn (string $slug) => Inertia::render('Blog/Show', ['post' => ['slug' => $slug]]))->name('show');
});

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/privacy-policy', fn () => Inertia::render('PrivacyPolicy'))->name('privacy-policy');

// ─── Admin Routes ─────────────────────────────────────────────────────────────

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', fn () => Inertia::render('Admin/Dashboard'))->name('dashboard');
    Route::get('/portfolio', fn () => Inertia::render('Admin/Portfolio/Index'))->name('portfolio');
    Route::get('/blog', fn () => Inertia::render('Admin/Blog/Index'))->name('blog');
    Route::get('/blog/create', fn () => Inertia::render('Admin/Blog/Form'))->name('blog.create');
    Route::get('/blog/{id}/edit', fn (int $id) => Inertia::render('Admin/Blog/Form', ['post' => ['id' => $id]]))->name('blog.edit');
    // Packages
    Route::get('/packages', [AdminPackageController::class, 'index'])->name('packages');
    Route::get('/packages/create', [AdminPackageController::class, 'create'])->name('packages.create');
    Route::post('/packages', [AdminPackageController::class, 'store'])->name('packages.store');
    Route::get('/packages/{package}/edit', [AdminPackageController::class, 'edit'])->name('packages.edit');
    Route::put('/packages/{package}', [AdminPackageController::class, 'update'])->name('packages.update');
    Route::delete('/packages/{package}', [AdminPackageController::class, 'destroy'])->name('packages.destroy');
    Route::patch('/packages/{package}/toggle', [AdminPackageController::class, 'toggleStatus'])->name('packages.toggle');
    Route::post('/packages/reorder', [AdminPackageController::class, 'reorder'])->name('packages.reorder');

    // Package Addons
    Route::get('/packages/addons', [PackageAddonController::class, 'index'])->name('packages.addons');
    Route::post('/packages/addons', [PackageAddonController::class, 'store'])->name('packages.addons.store');
    Route::put('/packages/addons/{addon}', [PackageAddonController::class, 'update'])->name('packages.addons.update');
    Route::delete('/packages/addons/{addon}', [PackageAddonController::class, 'destroy'])->name('packages.addons.destroy');
    Route::patch('/packages/addons/{addon}/toggle', [PackageAddonController::class, 'toggleStatus'])->name('packages.addons.toggle');
    Route::post('/packages/addons/reorder', [PackageAddonController::class, 'reorder'])->name('packages.addons.reorder');

    // Contacts
    Route::get('/contacts', [AdminContactController::class, 'index'])->name('contacts');
    Route::get('/contacts/{contact}', [AdminContactController::class, 'show'])->name('contacts.show');
    Route::patch('/contacts/{contact}/status', [AdminContactController::class, 'updateStatus'])->name('contacts.status');
    Route::delete('/contacts/{contact}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');

    // FAQ
    Route::get('/faq', [AdminFaqController::class, 'index'])->name('faq');
    Route::post('/faq', [AdminFaqController::class, 'store'])->name('faq.store');
    Route::put('/faq/{faq}', [AdminFaqController::class, 'update'])->name('faq.update');
    Route::delete('/faq/{faq}', [AdminFaqController::class, 'destroy'])->name('faq.destroy');
    Route::patch('/faq/{faq}/toggle', [AdminFaqController::class, 'toggleStatus'])->name('faq.toggle');
    Route::post('/faq/reorder', [AdminFaqController::class, 'reorder'])->name('faq.reorder');
});

// ─── Auth & Profile Routes ────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
