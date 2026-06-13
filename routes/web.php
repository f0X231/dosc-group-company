<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ─── Public Routes ────────────────────────────────────────────────────────────

Route::get('/', fn () => Inertia::render('Home'))->name('home');

Route::get('/portfolio', fn () => Inertia::render('Portfolio/Index'))->name('portfolio');

Route::prefix('services')->name('services.')->group(function () {
    Route::get('/', fn () => Inertia::render('Services/Index'))->name('index');
    Route::get('/website', fn () => Inertia::render('Services/Website'))->name('website');
    Route::get('/google-ads', fn () => Inertia::render('Services/GoogleAds'))->name('google-ads');
});

Route::get('/faq', fn () => Inertia::render('Faq'))->name('faq');

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', fn () => Inertia::render('Blog/Index'))->name('index');
    Route::get('/{slug}', fn (string $slug) => Inertia::render('Blog/Show', ['post' => ['slug' => $slug]]))->name('show');
});

Route::get('/contact', fn () => Inertia::render('Contact'))->name('contact');

Route::get('/privacy-policy', fn () => Inertia::render('PrivacyPolicy'))->name('privacy-policy');

// ─── Admin Routes ─────────────────────────────────────────────────────────────

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', fn () => Inertia::render('Admin/Dashboard'))->name('dashboard');
    Route::get('/portfolio', fn () => Inertia::render('Admin/Portfolio/Index'))->name('portfolio');
    Route::get('/blog', fn () => Inertia::render('Admin/Blog/Index'))->name('blog');
    Route::get('/blog/create', fn () => Inertia::render('Admin/Blog/Form'))->name('blog.create');
    Route::get('/blog/{id}/edit', fn (int $id) => Inertia::render('Admin/Blog/Form', ['post' => ['id' => $id]]))->name('blog.edit');
    Route::get('/contacts', fn () => Inertia::render('Admin/Contacts/Index'))->name('contacts');
});

// ─── Auth & Profile Routes ────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
