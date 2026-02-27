@extends('admin.layouts.app')

@section('header', 'Posts')

@section('content')
<div class="mb-4 flex items-center justify-between gap-4">
    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search posts..."
               class="rounded-md border border-gray-300 px-3 py-1.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
        <select name="status" class="rounded-md border border-gray-300 px-3 py-1.5 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
            <option value="">All statuses</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
        </select>
        <button type="submit" class="rounded-md bg-gray-100 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-200">Filter</button>
    </form>

    <a href="{{ route('posts.create') }}"
       class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
        New Post
    </a>
</div>

<div class="overflow-hidden bg-white shadow ring-1 ring-black/5 sm:rounded-lg">
    <table class="min-w-full divide-y divide-gray-300">
        <thead class="bg-gray-50">
            <tr>
                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Title</th>
                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Categories</th>
                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Reading</th>
                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
            @forelse($posts as $post)
            <tr>
                <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900">{{ $post->title }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm">
                    <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $post->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($post->status) }}
                    </span>
                </td>
                <td class="px-3 py-4 text-sm text-gray-500">{{ $post->categories->pluck('name')->implode(', ') }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $post->reading_time }} min</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $post->created_at->format('M d, Y') }}</td>
                <td class="whitespace-nowrap px-3 py-4 text-right text-sm">
                    <a href="{{ route('posts.edit', $post) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                    <form method="POST" action="{{ route('posts.destroy', $post) }}" class="inline ml-3"
                          onsubmit="return confirm('Delete this post?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500">No posts yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $posts->links() }}
</div>
@endsection
