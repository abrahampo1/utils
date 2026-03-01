@extends('admin.layouts.app')

@section('header', 'Plesk Sites')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('plesk.server') }}"
       class="bg-white text-black border-black border border-r-3 border-b-3 px-4 py-2 text-sm tinos-bold hover:bg-gray-100">
        Server Overview
    </a>
    <form method="POST" action="{{ route('plesk.refresh') }}">
        @csrf
        <button type="submit" class="bg-white text-black border-black border border-r-3 border-b-3 px-4 py-2 text-sm tinos-bold hover:bg-gray-100">
            Refresh Cache
        </button>
    </form>
</div>

@if(empty($domains))
    <div class="bg-white border-black border border-r-3 border-b-3 p-8 text-center">
        <p class="text-sm text-gray-600 tinos-regular">No sites found. Check your Plesk API configuration.</p>
    </div>
@else
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($domains as $site)
            <a href="{{ route('plesk.show', $site['name']) }}"
               class="block bg-white border-black border border-r-3 border-b-3 p-5 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm tinos-bold text-black truncate">{{ $site['name'] }}</h3>
                    @if(isset($site['hosting_type']))
                        <span class="inline-flex border-black border px-2 py-0.5 text-xs tinos-regular-italic">
                            {{ $site['hosting_type'] }}
                        </span>
                    @endif
                </div>
                <div class="flex items-center gap-3 text-xs text-gray-600 tinos-regular">
                    @if(isset($site['base_domain']))
                        <span>{{ $site['base_domain'] }}</span>
                    @endif
                    @if(isset($site['status']) && $site['status'] !== 0)
                        <span class="text-red-600">Suspended</span>
                    @else
                        <span>Active</span>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
@endif
@endsection
