@extends('admin.layouts.app')

@section('header')
    {{ $trackingLink->name }}
    <span class="text-base tinos-regular text-gray-500 ml-2">?ref={{ $trackingLink->code }}</span>
@endsection

@section('content')
{{-- Link Info --}}
<div class="mb-6 flex items-center justify-between">
    <div class="text-sm text-gray-600 tinos-regular">
        Usage: append <code class="text-black">?ref={{ $trackingLink->code }}</code> to any URL
    </div>
    <div class="flex gap-3">
        <a href="{{ route('tracking-links.edit', $trackingLink) }}" class="bg-white text-black border-black border border-r-3 border-b-3 px-4 py-2 text-sm tinos-bold">Edit</a>
        <a href="{{ route('tracking-links.index') }}" class="text-gray-600 hover:text-black text-sm tinos-regular py-2">Back to list</a>
    </div>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <div class="bg-white border-black border border-r-3 border-b-3 p-5">
        <div class="text-sm text-gray-600 tinos-regular">Total Clicks</div>
        <div class="mt-1 text-3xl tinos-bold text-black">{{ number_format($totalClicks) }}</div>
    </div>
    <div class="bg-white border-black border border-r-3 border-b-3 p-5">
        <div class="text-sm text-gray-600 tinos-regular">Recent Clicks (30d)</div>
        <div class="mt-1 text-3xl tinos-bold text-black">{{ number_format($recentClicks) }}</div>
    </div>
    <div class="bg-white border-black border border-r-3 border-b-3 p-5">
        <div class="text-sm text-gray-600 tinos-regular">Top Browser</div>
        <div class="mt-1 text-3xl tinos-bold text-black">{{ $topBrowser->browser ?? '—' }}</div>
    </div>
    <div class="bg-white border-black border border-r-3 border-b-3 p-5">
        <div class="text-sm text-gray-600 tinos-regular">Top Device</div>
        <div class="mt-1 text-3xl tinos-bold text-black">{{ $topDevice->device_type ?? '—' }}</div>
    </div>
</div>

{{-- Charts Row --}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
    {{-- Clicks Trend --}}
    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <h3 class="text-lg tinos-bold text-black mb-4">Clicks per Day (30 days)</h3>
        <canvas id="clicksTrendChart" height="200"></canvas>
    </div>

    {{-- Device Breakdown --}}
    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <h3 class="text-lg tinos-bold text-black mb-4">Device Breakdown</h3>
        @if($deviceBreakdown->isEmpty())
            <p class="text-sm text-gray-600">No click data yet.</p>
        @else
            <canvas id="deviceChart" height="200"></canvas>
        @endif
    </div>
</div>

{{-- Lists Row --}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-8">
    {{-- Top Browsers --}}
    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <h3 class="text-lg tinos-bold text-black mb-4">Top Browsers</h3>
        @if($topBrowsers->isEmpty())
            <p class="text-sm text-gray-600">No data yet.</p>
        @else
        <ul class="space-y-3">
            @foreach($topBrowsers as $browser)
            <li class="flex items-center justify-between border-b border-gray-200 pb-2">
                <span class="text-sm text-black">{{ $browser->browser }}</span>
                <span class="text-sm tinos-bold text-black">{{ number_format($browser->count) }}</span>
            </li>
            @endforeach
        </ul>
        @endif
    </div>

    {{-- Top Platforms --}}
    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <h3 class="text-lg tinos-bold text-black mb-4">Top Platforms</h3>
        @if($topPlatforms->isEmpty())
            <p class="text-sm text-gray-600">No data yet.</p>
        @else
        <ul class="space-y-3">
            @foreach($topPlatforms as $platform)
            <li class="flex items-center justify-between border-b border-gray-200 pb-2">
                <span class="text-sm text-black">{{ $platform->platform }}</span>
                <span class="text-sm tinos-bold text-black">{{ number_format($platform->count) }}</span>
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
                <span class="text-sm text-gray-600 truncate mr-3" title="{{ $ref->referer }}">{{ Str::limit($ref->referer, 40) }}</span>
                <span class="text-sm tinos-bold text-black whitespace-nowrap">{{ number_format($ref->count) }}</span>
            </li>
            @endforeach
        </ul>
        @endif
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
// Clicks Trend Chart
const clicksData = @json($clicksTrend);
const dates = clicksData.map(d => d.date);
const clicks = clicksData.map(d => d.clicks);

new Chart(document.getElementById('clicksTrendChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: dates,
        datasets: [{
            label: 'Clicks',
            data: clicks,
            borderColor: '#000000',
            backgroundColor: 'rgba(0, 0, 0, 0.05)',
            fill: true,
            tension: 0.3,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false,
            }
        },
        scales: {
            x: { grid: { display: false } },
            y: { beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});

// Device Breakdown Chart
const deviceData = @json($deviceBreakdown);
if (deviceData.length > 0) {
    const deviceColors = ['#000000', '#6B7280', '#9CA3AF', '#D1D5DB', '#E5E7EB'];
    new Chart(document.getElementById('deviceChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: deviceData.map(d => d.device_type),
            datasets: [{
                data: deviceData.map(d => d.count),
                backgroundColor: deviceColors.slice(0, deviceData.length),
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
