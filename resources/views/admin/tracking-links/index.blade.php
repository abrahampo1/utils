@extends('admin.layouts.app')

@section('header', 'Tracking Links')

@section('content')
<div class="mb-4 flex items-center justify-between gap-4">
    <form method="GET" action="{{ route('tracking-links.index') }}" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search links..."
               class="border-black border border-r-3 border-b-3 px-3 py-2 text-sm tinos-regular focus:outline-none w-64">
        <button type="submit" class="bg-white text-black border-black border border-r-3 border-b-3 px-4 py-2 text-sm tinos-bold hover:bg-gray-100">Search</button>
        @if(request('search'))
            <a href="{{ route('tracking-links.index') }}" class="px-4 py-2 text-sm tinos-regular text-gray-600 hover:text-black">Clear</a>
        @endif
    </form>
    <a href="{{ route('tracking-links.create') }}"
       class="bg-black text-white border-black border border-r-3 border-b-3 px-4 py-2 text-sm tinos-bold hover:bg-gray-800">
        New Link
    </a>
</div>

<div class="border-black border border-r-3 border-b-3 bg-white">
    <table class="min-w-full">
        <thead>
            <tr class="border-b border-black">
                <th class="py-3.5 pl-4 pr-3 text-left text-sm tinos-bold text-black">Name</th>
                <th class="px-3 py-3.5 text-left text-sm tinos-bold text-black">Code</th>
                <th class="px-3 py-3.5 text-left text-sm tinos-bold text-black">Clicks</th>
                <th class="px-3 py-3.5 text-left text-sm tinos-bold text-black">Status</th>
                <th class="px-3 py-3.5 text-right text-sm tinos-bold text-black">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($links as $link)
            <tr class="border-b border-black">
                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm tinos-bold text-black">{{ $link->name }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">
                    <code class="text-xs">?ref={{ $link->code }}</code>
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm tinos-bold text-black">{{ number_format($link->clicks_count) }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm">
                    <span class="inline-flex border-black border px-2 py-0.5 text-xs tinos-regular-italic">
                        {{ $link->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-right text-sm">
                    <a href="{{ route('tracking-links.show', $link) }}" class="text-black hover:underline tinos-bold">Stats</a>
                    <a href="{{ route('tracking-links.edit', $link) }}" class="text-black hover:underline tinos-bold ml-3">Edit</a>
                    <form method="POST" action="{{ route('tracking-links.destroy', $link) }}" class="inline ml-3"
                          onsubmit="return confirm('Delete this tracking link? All click data will be lost.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-black hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-3 py-8 text-center text-sm text-gray-600">No tracking links yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($links->hasPages())
<div class="mt-4">
    {{ $links->links() }}
</div>
@endif
@endsection
