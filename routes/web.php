<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\PleskController;
use App\Http\Controllers\Admin\RedirectLinkController;
use App\Http\Controllers\Admin\TrackingLinkController;
use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/r/{code}', [RedirectController::class, 'handle'])->name('redirect.handle');

Route::prefix('dashboard')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('posts', PostController::class);
        Route::resource('projects', ProjectController::class);
        Route::post('projects/reorder', [ProjectController::class, 'reorder'])->name('projects.reorder');

        Route::get('images', [ImageController::class, 'index'])->name('images.index');
        Route::post('images', [ImageController::class, 'store'])->name('images.store');
        Route::delete('images/{image}', [ImageController::class, 'destroy'])->name('images.destroy');

        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

        Route::resource('tracking-links', TrackingLinkController::class);
        Route::resource('redirect-links', RedirectLinkController::class);

        Route::prefix('plesk')->group(function () {
            Route::get('/', [PleskController::class, 'index'])->name('plesk.index');
            Route::get('/server', [PleskController::class, 'server'])->name('plesk.server');
            Route::post('/refresh', [PleskController::class, 'refresh'])->name('plesk.refresh');
            Route::get('/{domain}', [PleskController::class, 'show'])->name('plesk.show')
                ->where('domain', '[a-zA-Z0-9.\-]+');
            Route::post('/{domain}/git-pull', [PleskController::class, 'gitPull'])->name('plesk.git-pull')
                ->where('domain', '[a-zA-Z0-9.\-]+');
            Route::post('/{domain}/artisan', [PleskController::class, 'artisan'])->name('plesk.artisan')
                ->where('domain', '[a-zA-Z0-9.\-]+');
            Route::post('/{domain}/refresh', [PleskController::class, 'refresh'])->name('plesk.domain-refresh')
                ->where('domain', '[a-zA-Z0-9.\-]+');
        });
    });
});
