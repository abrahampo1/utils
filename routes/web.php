<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Hello!',
        'generated_at' => now()->toIso8601String(),
        'generated_at_timestamp' => now()->timestamp,
    ]);
});


Route::get('totalCommits/{username}', [App\Http\Controllers\GithubStatsController::class, 'getTotalCommits'])
    ->name('github.total_commits')
    ->where('username', '[a-zA-Z0-9\-_]+'); // Optional username parameter with regex validation