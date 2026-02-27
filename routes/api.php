<?php

use App\Http\Controllers\Api\AnalyticsApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\PostApiController;
use App\Http\Controllers\Api\ProjectApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Hello!',
        'generated_at' => now()->toIso8601String(),
        'generated_at_timestamp' => now()->timestamp,
    ]);
});

// GitHub Stats
Route::get('totalCommits/{username}', [App\Http\Controllers\GithubStatsController::class, 'getTotalCommits'])
    ->name('github.total_commits')
    ->where('username', '[a-zA-Z0-9\-_]+');

Route::get('contributions/{username}', [App\Http\Controllers\GithubStatsController::class, 'getContributions'])
    ->name('github.contributions')
    ->where('username', '[a-zA-Z0-9\-_]+');

// Blog
Route::get('posts', [PostApiController::class, 'index']);
Route::get('posts/{slug}', [PostApiController::class, 'show']);

// Projects
Route::get('projects', [ProjectApiController::class, 'index']);

// Categories
Route::get('categories', [CategoryApiController::class, 'index']);

// Analytics
Route::post('analytics/track', [AnalyticsApiController::class, 'track'])
    ->middleware('throttle:analytics');
Route::post('analytics/page-view', [AnalyticsApiController::class, 'trackPageView'])
    ->middleware('throttle:analytics');
Route::post('analytics/link-click', [AnalyticsApiController::class, 'trackLinkClick'])
    ->middleware('throttle:analytics');