@extends('admin.layouts.app')

@section('header', 'Dashboard')

@section('content')
{{-- Stat Cards --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <div class="bg-white border-black border border-r-3 border-b-3 p-5">
        <div class="text-sm text-gray-600 tinos-regular">Page Views (30d)</div>
        <div class="mt-1 text-3xl tinos-bold text-black">{{ number_format($totalPageViews) }}</div>
    </div>
    <div class="bg-white border-black border border-r-3 border-b-3 p-5">
        <div class="text-sm text-gray-600 tinos-regular">Post Views (30d)</div>
        <div class="mt-1 text-3xl tinos-bold text-black">{{ number_format($totalPostViews) }}</div>
    </div>
    <div class="bg-white border-black border border-r-3 border-b-3 p-5">
        <div class="text-sm text-gray-600 tinos-regular">Unique Visitors (30d)</div>
        <div class="mt-1 text-3xl tinos-bold text-black">{{ number_format($uniqueVisitors) }}</div>
    </div>
    <div class="bg-white border-black border border-r-3 border-b-3 p-5">
        <div class="text-sm text-gray-600 tinos-regular">Link Clicks (30d)</div>
        <div class="mt-1 text-3xl tinos-bold text-black">{{ number_format($totalLinkClicks) }}</div>
    </div>
</div>

{{-- Charts Row --}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
    {{-- Traffic Trend --}}
    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <h3 class="text-lg tinos-bold text-black mb-4">Traffic Trend (30 days)</h3>
        <canvas id="trafficTrendChart" height="200"></canvas>
    </div>

    {{-- Top Pages --}}
    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <h3 class="text-lg tinos-bold text-black mb-4">Top Pages</h3>
        @if($topPages->isEmpty())
            <p class="text-sm text-gray-600">No page views yet.</p>
        @else
        <ul class="space-y-3">
            @foreach($topPages as $page)
            <li class="flex items-center justify-between border-b border-gray-200 pb-2">
                <span class="text-sm text-black truncate mr-3" title="{{ $page->path }}">{{ $page->path }}</span>
                <div class="flex gap-3 whitespace-nowrap">
                    <span class="text-sm tinos-bold text-black">{{ number_format($page->count) }}</span>
                    <span class="text-xs text-gray-500 tinos-regular-italic">{{ number_format($page->unique_count) }} unique</span>
                </div>
            </li>
            @endforeach
        </ul>
        @endif
    </div>
</div>

{{-- Middle Row --}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
    {{-- Popular Posts --}}
    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <h3 class="text-lg tinos-bold text-black mb-4">Popular Posts</h3>
        @if($popularPosts->isEmpty())
            <p class="text-sm text-gray-600">No post views yet.</p>
        @else
        <ul class="space-y-3">
            @foreach($popularPosts as $post)
            <li class="flex items-center justify-between border-b border-gray-200 pb-2">
                <a href="{{ route('posts.edit', $post) }}" class="text-sm text-black hover:underline truncate mr-3">{{ $post->title }}</a>
                <span class="text-sm tinos-bold text-black whitespace-nowrap">{{ number_format($post->views_count) }} views</span>
            </li>
            @endforeach
        </ul>
        @endif
    </div>

    {{-- Top Referrers --}}
    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <h3 class="text-lg tinos-bold text-black mb-4">Top Referrers</h3>
        @if($topReferrers->isEmpty())
            <p class="text-sm text-gray-600">No referrer data yet.</p>
        @else
        <ul class="space-y-3">
            @foreach($topReferrers as $ref)
            <li class="flex items-center justify-between border-b border-gray-200 pb-2">
                <span class="text-sm text-gray-600 truncate mr-3">{{ $ref->referer }}</span>
                <span class="text-sm tinos-bold text-black whitespace-nowrap">{{ number_format($ref->count) }}</span>
            </li>
            @endforeach
        </ul>
        @endif
    </div>
</div>

{{-- UTM Analytics Row --}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
    {{-- Source Breakdown Chart --}}
    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <h3 class="text-lg tinos-bold text-black mb-4">Traffic Sources (30d)</h3>
        @if($utmSourceBreakdown->isEmpty())
            <p class="text-sm text-gray-600">No UTM data yet.</p>
        @else
            <canvas id="utmSourceChart" height="200"></canvas>
        @endif
    </div>

    {{-- Top Tracking Links --}}
    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <h3 class="text-lg tinos-bold text-black mb-4">Top Tracking Links (30d)</h3>
        @if($topTrackingLinks->isEmpty())
            <p class="text-sm text-gray-600">No link clicks yet.</p>
        @else
        <ul class="space-y-3">
            @foreach($topTrackingLinks as $link)
            <li class="flex items-center justify-between border-b border-gray-200 pb-2">
                <a href="{{ route('tracking-links.show', $link) }}" class="text-sm text-black hover:underline truncate mr-3">{{ $link->name }}</a>
                <span class="text-sm tinos-bold text-black whitespace-nowrap">{{ number_format($link->clicks_count) }} clicks</span>
            </li>
            @endforeach
        </ul>
        @endif
    </div>
</div>

{{-- UTM Details Row --}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-8">
    {{-- Top Sources --}}
    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <h3 class="text-lg tinos-bold text-black mb-4">Top Sources</h3>
        @if($utmSources->isEmpty())
            <p class="text-sm text-gray-600">No UTM source data yet.</p>
        @else
        <ul class="space-y-3">
            @foreach($utmSources as $source)
            <li class="flex items-center justify-between border-b border-gray-200 pb-2">
                <span class="text-sm text-black">{{ $source->utm_source }}</span>
                <span class="text-sm tinos-bold text-black">{{ number_format($source->count) }}</span>
            </li>
            @endforeach
        </ul>
        @endif
    </div>

    {{-- Top Mediums --}}
    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <h3 class="text-lg tinos-bold text-black mb-4">Top Mediums</h3>
        @if($utmMediums->isEmpty())
            <p class="text-sm text-gray-600">No UTM medium data yet.</p>
        @else
        <ul class="space-y-3">
            @foreach($utmMediums as $medium)
            <li class="flex items-center justify-between border-b border-gray-200 pb-2">
                <span class="text-sm text-black">{{ $medium->utm_medium }}</span>
                <span class="text-sm tinos-bold text-black">{{ number_format($medium->count) }}</span>
            </li>
            @endforeach
        </ul>
        @endif
    </div>

    {{-- Top Campaigns --}}
    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <h3 class="text-lg tinos-bold text-black mb-4">Top Campaigns</h3>
        @if($utmCampaigns->isEmpty())
            <p class="text-sm text-gray-600">No UTM campaign data yet.</p>
        @else
        <ul class="space-y-3">
            @foreach($utmCampaigns as $campaign)
            <li class="flex items-center justify-between border-b border-gray-200 pb-2">
                <span class="text-sm text-black">{{ $campaign->utm_campaign }}</span>
                <span class="text-sm tinos-bold text-black">{{ number_format($campaign->count) }}</span>
            </li>
            @endforeach
        </ul>
        @endif
    </div>
</div>

{{-- Bottom Row --}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    {{-- Recent Posts --}}
    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <h3 class="text-lg tinos-bold text-black mb-4">Recent Posts</h3>
        @if($recentPosts->isEmpty())
            <p class="text-sm text-gray-600">No posts yet.</p>
        @else
        <ul class="space-y-3">
            @foreach($recentPosts as $post)
            <li class="flex items-center justify-between border-b border-gray-200 pb-2">
                <div>
                    <a href="{{ route('posts.edit', $post) }}" class="text-sm text-black hover:underline">{{ $post->title }}</a>
                    <span class="ml-2 inline-flex border-black border px-2 py-0.5 text-xs tinos-regular-italic">
                        {{ ucfirst($post->status) }}
                    </span>
                </div>
                <span class="text-xs text-gray-600 whitespace-nowrap">{{ $post->created_at->format('M d') }}</span>
            </li>
            @endforeach
        </ul>
        @endif
    </div>

    {{-- Overview --}}
    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <h3 class="text-lg tinos-bold text-black mb-4">Overview</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="border border-gray-200 p-3">
                <div class="text-xs text-gray-500 tinos-regular">Total Posts</div>
                <div class="text-xl tinos-bold">{{ $counts['posts'] }}</div>
            </div>
            <div class="border border-gray-200 p-3">
                <div class="text-xs text-gray-500 tinos-regular">Published</div>
                <div class="text-xl tinos-bold">{{ $counts['published'] }}</div>
            </div>
            <div class="border border-gray-200 p-3">
                <div class="text-xs text-gray-500 tinos-regular">Drafts</div>
                <div class="text-xl tinos-bold">{{ $counts['drafts'] }}</div>
            </div>
            <div class="border border-gray-200 p-3">
                <div class="text-xs text-gray-500 tinos-regular">Projects</div>
                <div class="text-xl tinos-bold">{{ $counts['projects'] }}</div>
            </div>
            <div class="border border-gray-200 p-3">
                <div class="text-xs text-gray-500 tinos-regular">Tracking Links</div>
                <div class="text-xl tinos-bold">{{ $counts['tracking_links'] }}</div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
const pageData = @json($pageViewsTrend);
const postData = @json($postViewsTrend);

// Build a unified set of dates from both datasets
const allDates = new Set([...pageData.map(d => d.date), ...postData.map(d => d.date)]);
const sortedDates = [...allDates].sort();

const pageMap = Object.fromEntries(pageData.map(d => [d.date, d.views]));
const postMap = Object.fromEntries(postData.map(d => [d.date, d.views]));

const chartColors = ['#000000', '#374151', '#6B7280', '#9CA3AF', '#D1D5DB', '#E5E7EB'];

const ctx = document.getElementById('trafficTrendChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: sortedDates,
        datasets: [
            {
                label: 'Page Views',
                data: sortedDates.map(d => pageMap[d] || 0),
                borderColor: '#000000',
                backgroundColor: 'rgba(0, 0, 0, 0.05)',
                fill: true,
                tension: 0.3,
            },
            {
                label: 'Post Views',
                data: sortedDates.map(d => postMap[d] || 0),
                borderColor: '#9CA3AF',
                backgroundColor: 'rgba(156, 163, 175, 0.05)',
                fill: true,
                tension: 0.3,
                borderDash: [5, 5],
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                labels: { font: { family: 'Tinos' }, boxWidth: 20 }
            }
        },
        scales: {
            x: { grid: { display: false } },
            y: { beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});

// UTM Source Breakdown Chart
const utmSourceData = @json($utmSourceBreakdown);
if (utmSourceData.length > 0) {
    new Chart(document.getElementById('utmSourceChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: utmSourceData.map(d => d.utm_source),
            datasets: [{
                data: utmSourceData.map(d => d.count),
                backgroundColor: chartColors.slice(0, utmSourceData.length),
                borderWidth: 2,
                borderColor: '#ffffff',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { family: 'Tinos' }, padding: 16 }
                }
            }
        }
    });
}
</script>
@endsection
@endsection
