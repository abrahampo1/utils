@extends('admin.layouts.app')

@section('header')
    {{ $domain }}
    @if($isLaravel)
        <span class="inline-flex border-black border px-2 py-0.5 text-xs tinos-regular-italic ml-2 align-middle">Laravel</span>
    @endif
@endsection

@section('content')
{{-- Action Bar --}}
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('plesk.index') }}" class="text-gray-600 hover:text-black text-sm tinos-regular">Back to sites</a>
    <form method="POST" action="{{ route('plesk.domain-refresh', $domain) }}">
        @csrf
        <button type="submit" class="bg-white text-black border-black border border-r-3 border-b-3 px-4 py-2 text-sm tinos-bold hover:bg-gray-100">
            Refresh
        </button>
    </form>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <div class="bg-white border-black border border-r-3 border-b-3 p-5">
        <div class="text-sm text-gray-600 tinos-regular">Status</div>
        <div class="mt-1 text-xl tinos-bold text-black">
            @if(isset($domainInfo['status']) && $domainInfo['status'] !== 0)
                Suspended
            @else
                Active
            @endif
        </div>
    </div>

    <div class="bg-white border-black border border-r-3 border-b-3 p-5">
        <div class="text-sm text-gray-600 tinos-regular">PHP Version</div>
        <div class="mt-1 text-xl tinos-bold text-black">{{ $detail['php_version'] ?? '—' }}</div>
    </div>

    <div class="bg-white border-black border border-r-3 border-b-3 p-5">
        <div class="text-sm text-gray-600 tinos-regular">Disk Usage</div>
        <div class="mt-1 text-xl tinos-bold text-black">{{ $detail['disk_usage'] ?? '—' }}</div>
    </div>

    <div class="bg-white border-black border border-r-3 border-b-3 p-5">
        <div class="text-sm text-gray-600 tinos-regular">SSL</div>
        <div class="mt-1 text-xl tinos-bold text-black">{{ $detail['ssl'] ? 'Enabled' : 'Disabled' }}</div>
    </div>
</div>

{{-- Hosting Details + Databases --}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <h3 class="text-lg tinos-bold text-black mb-4">Hosting Details</h3>
        <dl class="space-y-3">
            @if($detail['doc_root'])
                <div class="border-b border-gray-200 pb-2">
                    <dt class="text-xs text-gray-500 tinos-regular">Document Root</dt>
                    <dd class="text-sm text-black tinos-bold mt-0.5 break-all">{{ $detail['doc_root'] }}</dd>
                </div>
            @endif
            @if($detail['ip'])
                <div class="border-b border-gray-200 pb-2">
                    <dt class="text-xs text-gray-500 tinos-regular">IP Address</dt>
                    <dd class="text-sm text-black tinos-bold mt-0.5">{{ $detail['ip'] }}</dd>
                </div>
            @endif
            @foreach($detail['hosting_properties'] as $propName => $propValue)
                <div class="border-b border-gray-200 pb-2">
                    <dt class="text-xs text-gray-500 tinos-regular">{{ str_replace('_', ' ', $propName) }}</dt>
                    <dd class="text-sm text-black tinos-bold mt-0.5 break-all">{{ Str::limit($propValue, 80) }}</dd>
                </div>
            @endforeach
            @if(isset($domainInfo['ip_addresses']) && is_array($domainInfo['ip_addresses']))
                <div class="border-b border-gray-200 pb-2">
                    <dt class="text-xs text-gray-500 tinos-regular">IP Addresses</dt>
                    <dd class="text-sm text-black tinos-bold mt-0.5">{{ implode(', ', $domainInfo['ip_addresses']) }}</dd>
                </div>
            @endif
        </dl>
    </div>

    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <h3 class="text-lg tinos-bold text-black mb-4">Databases</h3>
        @if(empty($databases))
            <p class="text-sm text-gray-600 tinos-regular">No databases found.</p>
        @else
            <ul class="space-y-3">
                @foreach($databases as $db)
                    <li class="border-b border-gray-200 pb-2">
                        <div class="text-sm tinos-bold text-black">{{ $db['name'] ?? $db['db-name'] ?? 'Unknown' }}</div>
                        <div class="text-xs text-gray-500 tinos-regular">
                            {{ $db['type'] ?? $db['db-server-type'] ?? '' }}
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

{{-- Git + Laravel --}}
<div class="grid grid-cols-1 gap-6 {{ $isLaravel ? 'lg:grid-cols-2' : '' }} mb-8">
    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg tinos-bold text-black">Git Commits</h3>
            <form method="POST" action="{{ route('plesk.git-pull', $domain) }}"
                  onsubmit="return confirm('Run git pull on {{ $domain }}?')">
                @csrf
                <button type="submit" class="bg-black text-white border-black border border-r-3 border-b-3 px-3 py-1 text-xs tinos-bold hover:bg-gray-800">
                    Git Pull
                </button>
            </form>
        </div>
        @if(empty($commits))
            <p class="text-sm text-gray-600 tinos-regular">No git repository found or no commits available.</p>
        @else
            <ul class="space-y-3">
                @foreach($commits as $commit)
                    <li class="border-b border-gray-200 pb-2">
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-gray-100 px-1.5 py-0.5 shrink-0">{{ $commit['short_hash'] }}</code>
                            <span class="text-sm text-black tinos-regular">{{ Str::limit($commit['message'], 60) }}</span>
                        </div>
                        <div class="text-xs text-gray-500 tinos-regular mt-1">
                            {{ $commit['author'] }} &middot; {{ $commit['date'] }}
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    @if($isLaravel)
        <div class="bg-white border-black border border-r-3 border-b-3 p-6">
            <h3 class="text-lg tinos-bold text-black mb-4">Laravel Quick Actions</h3>
            <div class="grid grid-cols-2 gap-2">
                @foreach($artisanCommands as $cmd)
                    <form method="POST" action="{{ route('plesk.artisan', $domain) }}"
                          onsubmit="return confirm('Run \'php artisan {{ $cmd }}\' on {{ $domain }}?')">
                        @csrf
                        <input type="hidden" name="command" value="{{ $cmd }}">
                        <button type="submit"
                                class="w-full bg-white text-black border-black border border-r-3 border-b-3 px-3 py-2 text-xs tinos-bold hover:bg-gray-100 text-left">
                            {{ $cmd }}
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
