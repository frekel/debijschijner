<?php

use App\Http\Controllers\ApplyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SitePageController;
use App\Http\Controllers\WpAssetController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SitePageController::class, 'show']);
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
Route::post('/aanvraag', [ApplyController::class, 'submit'])->name('apply.submit');

Route::get('wp-content/{path}', [WpAssetController::class, 'serveWpContent'])
    ->where('path', '.*');

Route::get('wp-includes/{path}', [WpAssetController::class, 'serveWpIncludes'])
    ->where('path', '.*');

Route::get('{path}', [SitePageController::class, 'show'])
    ->where('path', '.*');
