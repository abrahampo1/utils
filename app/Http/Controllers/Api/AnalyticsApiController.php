<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use App\Models\Post;
use App\Models\PostView;
use Illuminate\Http\Request;

class AnalyticsApiController extends Controller
{
    public function track(Request $request)
    {
        $data = $request->validate([
            'slug' => ['required', 'string'],
        ]);

        $post = Post::where('slug', $data['slug'])->first();

        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        PostView::create([
            'post_id' => $post->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'session_id' => md5($request->ip() . $request->userAgent()),
            'viewed_at' => now(),
        ]);

        return response()->json(['tracked' => true]);
    }

    public function trackPageView(Request $request)
    {
        $data = $request->validate([
            'path' => ['required', 'string', 'max:500'],
        ]);

        PageView::create([
            'path' => $data['path'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'session_id' => md5($request->ip() . $request->userAgent()),
            'viewed_at' => now(),
        ]);

        return response()->json(['tracked' => true]);
    }
}
