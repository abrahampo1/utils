<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RedirectLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RedirectLinkController extends Controller
{
    public function index(Request $request)
    {
        $query = RedirectLink::withCount('clicks')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%')
                  ->orWhere('destination_url', 'like', '%' . $request->search . '%');
            });
        }

        $links = $query->paginate(15)->withQueryString();

        return view('admin.redirect-links.index', compact('links'));
    }

    public function create()
    {
        return view('admin.redirect-links.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:redirect_links,code', 'regex:/^[a-z0-9\-]+$/'],
            'destination_url' => ['required', 'url', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        RedirectLink::create($data);

        return redirect()->route('redirect-links.index')->with('success', 'Redirect link created.');
    }

    public function show(RedirectLink $redirectLink)
    {
        $thirtyDaysAgo = now()->subDays(30);

        $totalClicks = $redirectLink->clicks()->count();
        $recentClicks = $redirectLink->clicks()->where('clicked_at', '>=', $thirtyDaysAgo)->count();

        $topBrowser = $redirectLink->clicks()
            ->select('browser', DB::raw('COUNT(*) as count'))
            ->whereNotNull('browser')
            ->groupBy('browser')
            ->orderByDesc('count')
            ->first();

        $topDevice = $redirectLink->clicks()
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->whereNotNull('device_type')
            ->groupBy('device_type')
            ->orderByDesc('count')
            ->first();

        // Clicks per day (30d)
        $clicksTrend = $redirectLink->clicks()
            ->where('clicked_at', '>=', $thirtyDaysAgo)
            ->select(DB::raw("DATE(clicked_at) as date"), DB::raw('COUNT(*) as clicks'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Device breakdown
        $deviceBreakdown = $redirectLink->clicks()
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->whereNotNull('device_type')
            ->groupBy('device_type')
            ->orderByDesc('count')
            ->get();

        // Top browsers
        $topBrowsers = $redirectLink->clicks()
            ->select('browser', DB::raw('COUNT(*) as count'))
            ->whereNotNull('browser')
            ->groupBy('browser')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Top platforms
        $topPlatforms = $redirectLink->clicks()
            ->select('platform', DB::raw('COUNT(*) as count'))
            ->whereNotNull('platform')
            ->groupBy('platform')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Top referrers
        $topReferrers = $redirectLink->clicks()
            ->select('referer', DB::raw('COUNT(*) as count'))
            ->whereNotNull('referer')
            ->where('referer', '!=', '')
            ->groupBy('referer')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return view('admin.redirect-links.show', compact(
            'redirectLink', 'totalClicks', 'recentClicks',
            'topBrowser', 'topDevice', 'clicksTrend', 'deviceBreakdown',
            'topBrowsers', 'topPlatforms', 'topReferrers'
        ));
    }

    public function edit(RedirectLink $redirectLink)
    {
        return view('admin.redirect-links.edit', compact('redirectLink'));
    }

    public function update(Request $request, RedirectLink $redirectLink)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:redirect_links,code,' . $redirectLink->id, 'regex:/^[a-z0-9\-]+$/'],
            'destination_url' => ['required', 'url', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $redirectLink->update($data);

        return redirect()->route('redirect-links.index')->with('success', 'Redirect link updated.');
    }

    public function destroy(RedirectLink $redirectLink)
    {
        $redirectLink->delete();

        return redirect()->route('redirect-links.index')->with('success', 'Redirect link deleted.');
    }
}
