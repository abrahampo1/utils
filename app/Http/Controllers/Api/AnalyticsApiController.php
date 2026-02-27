<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LinkClick;
use App\Models\PageView;
use App\Models\Post;
use App\Models\PostView;
use App\Models\TrackingLink;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

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

    public function trackLinkClick(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $link = TrackingLink::active()->where('code', $data['code'])->first();

        if (!$link) {
            return response()->json(['error' => 'Link not found'], 404);
        }

        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());

        LinkClick::create([
            'tracking_link_id' => $link->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'browser' => $agent->browser() ?: null,
            'browser_version' => $agent->version($agent->browser()) ?: null,
            'platform' => $agent->platform() ?: null,
            'device_type' => $this->getDeviceType($agent),
            'clicked_at' => now(),
        ]);

        return response()->json(['tracked' => true]);
    }

    private function getDeviceType(Agent $agent): string
    {
        if ($agent->isTablet()) {
            return 'Tablet';
        }

        if ($agent->isMobile()) {
            return 'Mobile';
        }

        if ($agent->isDesktop()) {
            return 'Desktop';
        }

        return 'Other';
    }
}
