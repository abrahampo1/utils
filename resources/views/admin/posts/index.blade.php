@extends('admin.layouts.app')

@section('header', 'Posts')

@section('content')
<div class="mb-4 flex items-center justify-between gap-4">
    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search posts..."
               class="border-black border border-r-3 border-b-3 px-3 py-1.5 text-sm tinos-regular focus:outline-none">
        <select name="status" class="border-black border border-r-3 border-b-3 px-3 py-1.5 text-sm tinos-regular focus:outline-none">
            <option value="">All statuses</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
        </select>
        <button type="submit" class="bg-white text-black border-black border border-r-3 border-b-3 px-3 py-1.5 text-sm tinos-bold">Filter</button>
    </form>

    <a href="{{ route('posts.create') }}"
       class="bg-black text-white border-black border border-r-3 border-b-3 px-4 py-2 text-sm tinos-bold hover:bg-gray-800">
        New Post
    </a>
</div>

<div class="border-black border border-r-3 border-b-3 bg-white">
    <table class="min-w-full">
        <thead>
            <tr class="border-b border-black">
                <th class="py-3.5 pl-4 pr-3 text-left text-sm tinos-bold text-black">Title</th>
                <th class="px-3 py-3.5 text-left text-sm tinos-bold text-black">Status</th>
                <th class="px-3 py-3.5 text-left text-sm tinos-bold text-black">Categories</th>
                <th class="px-3 py-3.5 text-left text-sm tinos-bold text-black">Reading</th>
                <th class="px-3 py-3.5 text-left text-sm tinos-bold text-black">Date</th>
                <th class="px-3 py-3.5 text-right text-sm tinos-bold text-black">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($posts as $post)
            <tr class="border-b border-black">
                <td class="py-4 pl-4 pr-3 text-sm tinos-bold text-black">{{ $post->title }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm">
                    <span class="inline-flex border-black border px-2 py-0.5 text-xs tinos-regular-italic">
                        {{ ucfirst($post->status) }}
                    </span>
                </td>
                <td class="px-3 py-4 text-sm text-gray-600">{{ $post->categories->pluck('name')->implode(', ') }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">{{ $post->reading_time }} min</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">{{ $post->created_at->format('M d, Y') }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-right text-sm">
                    <a href="{{ route('posts.edit', $post) }}" class="text-black hover:underline tinos-bold">Edit</a>
                    <form method="POST" action="{{ route('posts.destroy', $post) }}" class="inline ml-3"
                          onsubmit="return confirm('Delete this post?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-black hover:underline">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-600">No posts yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $posts->links() }}
</div>
@endsection
