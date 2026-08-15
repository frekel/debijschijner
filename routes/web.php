<?php

use App\Http\Controllers\Admin\ContactSubmissionAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PageAdminController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SitePageController;
use App\Http\Controllers\WpAssetController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');

    Route::middleware('admin.auth')->group(function (): void {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('contact-submissions', [ContactSubmissionAdminController::class, 'index'])->name('contact-submissions.index');
        Route::get('contact-submissions/export', [ContactSubmissionAdminController::class, 'export'])->name('contact-submissions.export');

        Route::get('pages', [PageAdminController::class, 'index'])->name('pages.index');
        Route::get('pages/create', [PageAdminController::class, 'create'])->name('pages.create');
        Route::post('pages', [PageAdminController::class, 'store'])->name('pages.store');
        Route::get('pages/{page}/edit', [PageAdminController::class, 'edit'])->name('pages.edit');
        Route::put('pages/{page}', [PageAdminController::class, 'update'])->name('pages.update');
        Route::delete('pages/{page}', [PageAdminController::class, 'destroy'])->name('pages.destroy');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    });
});

Route::get('/', [SitePageController::class, 'show']);
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

Route::get('wp-content/{path}', [WpAssetController::class, 'serveWpContent'])
    ->where('path', '.*');

Route::get('wp-includes/{path}', [WpAssetController::class, 'serveWpIncludes'])
    ->where('path', '.*');

Route::get('{path}', [SitePageController::class, 'show'])
    ->where('path', '.*');
