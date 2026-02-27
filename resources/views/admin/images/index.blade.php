@extends('admin.layouts.app')

@section('header', 'Images')

@section('content')
<div class="mb-6 bg-white p-6 shadow sm:rounded-lg">
    <form method="POST" action="{{ route('images.store') }}" enctype="multipart/form-data" class="flex items-end gap-4">
        @csrf
        <div class="flex-1">
            <label for="image" class="block text-sm font-medium text-gray-700">Upload Image</label>
            <input type="file" name="image" id="image" accept="image/*" required
                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100">
        </div>
        <div class="flex-1">
            <label for="alt_text" class="block text-sm font-medium text-gray-700">Alt Text</label>
            <input type="text" name="alt_text" id="alt_text" placeholder="Describe the image..."
                   class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
        </div>
        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Upload</button>
    </form>
    @error('image') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
    @forelse($images as $image)
    <div class="group relative overflow-hidden rounded-lg bg-white shadow">
        <img src="{{ $image->url }}" alt="{{ $image->alt_text }}"
             class="aspect-square w-full object-cover">
        <div class="absolute inset-0 flex flex-col items-center justify-center gap-2 bg-black/60 opacity-0 transition group-hover:opacity-100">
            <button onclick="copyToClipboard('{{ $image->url }}')"
                    class="rounded bg-white/90 px-3 py-1 text-xs font-medium text-gray-900 hover:bg-white">Copy URL</button>
            <button onclick="copyToClipboard('![{{ $image->alt_text }}]({{ $image->url }})')"
                    class="rounded bg-white/90 px-3 py-1 text-xs font-medium text-gray-900 hover:bg-white">Copy Markdown</button>
            <form method="POST" action="{{ route('images.destroy', $image) }}"
                  onsubmit="return confirm('Delete this image?')">
                @csrf @method('DELETE')
                <button type="submit" class="rounded bg-red-500/90 px-3 py-1 text-xs font-medium text-white hover:bg-red-600">Delete</button>
            </form>
        </div>
        <div class="p-2">
            <p class="truncate text-xs text-gray-500" title="{{ $image->original_filename }}">{{ $image->original_filename }}</p>
        </div>
    </div>
    @empty
    <div class="col-span-full py-8 text-center text-sm text-gray-500">No images uploaded yet.</div>
    @endforelse
</div>

<div class="mt-4">
    {{ $images->links() }}
</div>

@section('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Copied to clipboard!');
    });
}
</script>
@endsection
@endsection
