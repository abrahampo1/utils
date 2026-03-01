@extends('admin.layouts.app')

@section('header', 'Plesk Server')

@section('content')
<div class="mb-6">
    <a href="{{ route('plesk.index') }}" class="text-gray-600 hover:text-black text-sm tinos-regular">Back to sites</a>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <div class="bg-white border-black border border-r-3 border-b-3 p-5">
        <div class="text-sm text-gray-600 tinos-regular">Total Sites</div>
        <div class="mt-1 text-3xl tinos-bold text-black">{{ count($domains) }}</div>
    </div>
    @if($server)
        @if(isset($server['hostname']))
            <div class="bg-white border-black border border-r-3 border-b-3 p-5">
                <div class="text-sm text-gray-600 tinos-regular">Hostname</div>
                <div class="mt-1 text-lg tinos-bold text-black truncate">{{ $server['hostname'] }}</div>
            </div>
        @endif
        @if(isset($server['platform']))
            <div class="bg-white border-black border border-r-3 border-b-3 p-5">
                <div class="text-sm text-gray-600 tinos-regular">Platform</div>
                <div class="mt-1 text-lg tinos-bold text-black">{{ $server['platform'] }}</div>
            </div>
        @endif
        @if(isset($server['panel_version']))
            <div class="bg-white border-black border border-r-3 border-b-3 p-5">
                <div class="text-sm text-gray-600 tinos-regular">Plesk Version</div>
                <div class="mt-1 text-lg tinos-bold text-black">{{ $server['panel_version'] }}</div>
            </div>
        @endif
    @endif
</div>

{{-- Server Details --}}
@if($server)
    <div class="bg-white border-black border border-r-3 border-b-3 p-6">
        <h3 class="text-lg tinos-bold text-black mb-4">Server Details</h3>
        <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            @foreach($server as $key => $value)
                @if(is_string($value) || is_numeric($value))
                    <div class="border-b border-gray-200 pb-2">
                        <dt class="text-xs text-gray-500 tinos-regular">{{ str_replace('_', ' ', ucfirst($key)) }}</dt>
                        <dd class="text-sm text-black tinos-bold mt-0.5 truncate">{{ $value }}</dd>
                    </div>
                @endif
            @endforeach
        </dl>
    </div>
@else
    <div class="bg-white border-black border border-r-3 border-b-3 p-8 text-center">
        <p class="text-sm text-gray-600 tinos-regular">Could not retrieve server information.</p>
    </div>
@endif
@endsection
