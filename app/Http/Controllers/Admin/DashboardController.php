<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostView;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $thirtyDaysAgo = now()->subDays(30);

        $totalViews = PostView::where('viewed_at', '>=', $thirtyDaysAgo)->count();

        $uniqueVisitors = PostView::where('viewed_at', '>=', $thirtyDaysAgo)
            ->distinct('session_id')
            ->count('session_id');

        $viewsTrend = PostView::where('viewed_at', '>=', $thirtyDaysAgo)
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

        $topReferrers = PostView::where('viewed_at', '>=', $thirtyDaysAgo)
            ->whereNotNull('referer')
            ->where('referer', '!=', '')
            ->select('referer', DB::raw('COUNT(*) as count'))
            ->groupBy('referer')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        $recentPosts = Post::latest()->limit(5)->get(['id', 'title', 'status', 'created_at']);

        $counts = [
            'posts' => Post::count(),
            'published' => Post::published()->count(),
            'drafts' => Post::draft()->count(),
            'projects' => Project::count(),
        ];

        return view('admin.dashboard.index', compact(
            'totalViews', 'uniqueVisitors', 'viewsTrend',
            'popularPosts', 'topReferrers', 'recentPosts', 'counts'
        ));
    }
}
