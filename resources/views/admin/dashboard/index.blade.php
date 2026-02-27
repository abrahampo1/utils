@extends('admin.layouts.app')

@section('header', 'Dashboard')

@section('content')
{{-- Stat Cards --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <div class="bg-white overflow-hidden shadow rounded-lg p-5">
        <div class="text-sm font-medium text-gray-500">Total Views (30d)</div>
        <div class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($totalViews) }}</div>
    </div>
    <div class="bg-white overflow-hidden shadow rounded-lg p-5">
        <div class="text-sm font-medium text-gray-500">Unique Visitors (30d)</div>
        <div class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($uniqueVisitors) }}</div>
    </div>
    <div class="bg-white overflow-hidden shadow rounded-lg p-5">
        <div class="text-sm font-medium text-gray-500">Published Posts</div>
        <div class="mt-1 text-3xl font-bold text-gray-900">{{ $counts['published'] }}</div>
    </div>
    <div class="bg-white overflow-hidden shadow rounded-lg p-5">
        <div class="text-sm font-medium text-gray-500">Projects</div>
        <div class="mt-1 text-3xl font-bold text-gray-900">{{ $counts['projects'] }}</div>
    </div>
</div>

{{-- Charts Row --}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
    {{-- Views Trend --}}
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Views Trend (30 days)</h3>
        <canvas id="viewsTrendChart" height="200"></canvas>
    </div>

    {{-- Popular Posts --}}
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Popular Posts</h3>
        @if($popularPosts->isEmpty())
            <p class="text-sm text-gray-500">No views yet.</p>
        @else
        <ul class="space-y-3">
            @foreach($popularPosts as $post)
            <li class="flex items-center justify-between">
                <a href="{{ route('posts.edit', $post) }}" class="text-sm text-indigo-600 hover:underline truncate mr-3">{{ $post->title }}</a>
                <span class="text-sm font-medium text-gray-900 whitespace-nowrap">{{ number_format($post->views_count) }} views</span>
            </li>
            @endforeach
        </ul>
        @endif
    </div>
</div>

{{-- Bottom Row --}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    {{-- Top Referrers --}}
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Top Referrers</h3>
        @if($topReferrers->isEmpty())
            <p class="text-sm text-gray-500">No referrer data yet.</p>
        @else
        <ul class="space-y-3">
            @foreach($topReferrers as $ref)
            <li class="flex items-center justify-between">
                <span class="text-sm text-gray-600 truncate mr-3">{{ $ref->referer }}</span>
                <span class="text-sm font-medium text-gray-900 whitespace-nowrap">{{ number_format($ref->count) }}</span>
            </li>
            @endforeach
        </ul>
        @endif
    </div>

    {{-- Recent Posts --}}
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Posts</h3>
        @if($recentPosts->isEmpty())
            <p class="text-sm text-gray-500">No posts yet.</p>
        @else
        <ul class="space-y-3">
            @foreach($recentPosts as $post)
            <li class="flex items-center justify-between">
                <div>
                    <a href="{{ route('posts.edit', $post) }}" class="text-sm text-indigo-600 hover:underline">{{ $post->title }}</a>
                    <span class="ml-2 inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $post->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($post->status) }}
                    </span>
                </div>
                <span class="text-xs text-gray-500 whitespace-nowrap">{{ $post->created_at->format('M d') }}</span>
            </li>
            @endforeach
        </ul>
        @endif
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
const trendData = @json($viewsTrend);
const ctx = document.getElementById('viewsTrendChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: trendData.map(d => d.date),
        datasets: [{
            label: 'Views',
            data: trendData.map(d => d.views),
            borderColor: '#4f46e5',
            backgroundColor: 'rgba(79, 70, 229, 0.1)',
            fill: true,
            tension: 0.3,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false } },
            y: { beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});
</script>
@endsection
@endsection
