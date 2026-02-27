<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LinkClick;
use App\Models\PageView;
use App\Models\Post;
use App\Models\PostView;
use App\Models\Project;
use App\Models\TrackingLink;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $thirtyDaysAgo = now()->subDays(30);

        // Post views
        $totalPostViews = PostView::where('viewed_at', '>=', $thirtyDaysAgo)->count();

        // Page views
        $totalPageViews = PageView::where('viewed_at', '>=', $thirtyDaysAgo)->count();

        // Unique visitors (combined from both tables)
        $postSessions = PostView::where('viewed_at', '>=', $thirtyDaysAgo)
            ->whereNotNull('session_id')
            ->select('session_id');
        $pageSessions = PageView::where('viewed_at', '>=', $thirtyDaysAgo)
            ->whereNotNull('session_id')
            ->select('session_id');
        $uniqueVisitors = $postSessions->union($pageSessions)->distinct()->count('session_id');

        // Page views trend
        $pageViewsTrend = PageView::where('viewed_at', '>=', $thirtyDaysAgo)
            ->select(DB::raw("DATE(viewed_at) as date"), DB::raw('COUNT(*) as views'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Post views trend
        $postViewsTrend = PostView::where('viewed_at', '>=', $thirtyDaysAgo)
            ->select(DB::raw("DATE(viewed_at) as date"), DB::raw('COUNT(*) as views'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $popularPosts = Post::withCount(['views' => function ($query) use ($thirtyDaysAgo) {
                $query->where('viewed_at', '>=', $thirtyDaysAgo);
            }])
            ->whereHas('views', function ($query) use ($thirtyDaysAgo) {
                $query->where('viewed_at', '>=', $thirtyDaysAgo);
            })
            ->orderByDesc('views_count')
            ->limit(5)
            ->get(['id', 'title', 'slug']);

        // Top pages
        $topPages = PageView::where('viewed_at', '>=', $thirtyDaysAgo)
            ->select('path', DB::raw('COUNT(*) as count'), DB::raw('COUNT(DISTINCT session_id) as unique_count'))
            ->groupBy('path')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Top referrers (combined from both tables)
        $postReferrers = PostView::where('viewed_at', '>=', $thirtyDaysAgo)
            ->whereNotNull('referer')
            ->where('referer', '!=', '')
            ->select('referer');
        $pageReferrers = PageView::where('viewed_at', '>=', $thirtyDaysAgo)
            ->whereNotNull('referer')
            ->where('referer', '!=', '')
            ->select('referer');
        $topReferrers = $pageReferrers->union($postReferrers)
            ->get()
            ->groupBy('referer')
            ->map(fn($items, $referer) => (object) ['referer' => $referer, 'count' => $items->count()])
            ->sortByDesc('count')
            ->take(5)
            ->values();

        $recentPosts = Post::latest()->limit(5)->get(['id', 'title', 'status', 'created_at']);

        $totalLinkClicks = LinkClick::where('clicked_at', '>=', $thirtyDaysAgo)->count();

        $counts = [
            'posts' => Post::count(),
            'published' => Post::published()->count(),
            'drafts' => Post::draft()->count(),
            'projects' => Project::count(),
            'tracking_links' => TrackingLink::count(),
        ];

        return view('admin.dashboard.index', compact(
            'totalPostViews', 'totalPageViews', 'uniqueVisitors', 'totalLinkClicks',
            'pageViewsTrend', 'postViewsTrend',
            'popularPosts', 'topPages', 'topReferrers', 'recentPosts', 'counts'
        ));
    }
}
