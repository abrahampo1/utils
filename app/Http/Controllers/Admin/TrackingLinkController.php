<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrackingLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackingLinkController extends Controller
{
    public function index(Request $request)
    {
        $query = TrackingLink::withCount('clicks')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        $links = $query->paginate(15)->withQueryString();

        return view('admin.tracking-links.index', compact('links'));
    }

    public function create()
    {
        return view('admin.tracking-links.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:tracking_links,code', 'regex:/^[a-z0-9\-]+$/'],
            'destination_url' => ['required', 'url', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        TrackingLink::create($data);

        return redirect()->route('tracking-links.index')->with('success', 'Tracking link created.');
    }

    public function show(TrackingLink $trackingLink)
    {
        $thirtyDaysAgo = now()->subDays(30);

        $totalClicks = $trackingLink->clicks()->count();
        $recentClicks = $trackingLink->clicks()->where('clicked_at', '>=', $thirtyDaysAgo)->count();

        $topBrowser = $trackingLink->clicks()
            ->select('browser', DB::raw('COUNT(*) as count'))
            ->whereNotNull('browser')
            ->groupBy('browser')
            ->orderByDesc('count')
            ->first();

        $topDevice = $trackingLink->clicks()
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->whereNotNull('device_type')
            ->groupBy('device_type')
            ->orderByDesc('count')
            ->first();

        // Clicks per day (30d)
        $clicksTrend = $trackingLink->clicks()
            ->where('clicked_at', '>=', $thirtyDaysAgo)
            ->select(DB::raw("DATE(clicked_at) as date"), DB::raw('COUNT(*) as clicks'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Device breakdown
        $deviceBreakdown = $trackingLink->clicks()
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->whereNotNull('device_type')
            ->groupBy('device_type')
            ->orderByDesc('count')
            ->get();

        // Top browsers
        $topBrowsers = $trackingLink->clicks()
            ->select('browser', DB::raw('COUNT(*) as count'))
            ->whereNotNull('browser')
            ->groupBy('browser')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Top platforms
        $topPlatforms = $trackingLink->clicks()
            ->select('platform', DB::raw('COUNT(*) as count'))
            ->whereNotNull('platform')
            ->groupBy('platform')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Top referrers
        $topReferrers = $trackingLink->clicks()
            ->select('referer', DB::raw('COUNT(*) as count'))
            ->whereNotNull('referer')
            ->where('referer', '!=', '')
            ->groupBy('referer')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return view('admin.tracking-links.show', compact(
            'trackingLink', 'totalClicks', 'recentClicks',
            'topBrowser', 'topDevice', 'clicksTrend', 'deviceBreakdown',
            'topBrowsers', 'topPlatforms', 'topReferrers'
        ));
    }

    public function edit(TrackingLink $trackingLink)
    {
        return view('admin.tracking-links.edit', compact('trackingLink'));
    }

    public function update(Request $request, TrackingLink $trackingLink)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:tracking_links,code,' . $trackingLink->id, 'regex:/^[a-z0-9\-]+$/'],
            'destination_url' => ['required', 'url', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $trackingLink->update($data);

        return redirect()->route('tracking-links.index')->with('success', 'Tracking link updated.');
    }

    public function destroy(TrackingLink $trackingLink)
    {
        $trackingLink->delete();

        return redirect()->route('tracking-links.index')->with('success', 'Tracking link deleted.');
    }
}
