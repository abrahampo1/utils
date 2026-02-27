@extends('admin.layouts.app')

@section('header', 'Images')

@section('content')
<div class="mb-6 bg-white border-black border border-r-3 border-b-3 p-6">
    <form method="POST" action="{{ route('images.store') }}" enctype="multipart/form-data" class="flex items-end gap-4">
        @csrf
        <div class="flex-1">
            <label for="image" class="block text-sm tinos-bold text-black">Upload Image</label>
            <input type="file" name="image" id="image" accept="image/*" required
                   class="mt-1 block w-full text-sm text-black file:mr-4 file:border-black file:border file:border-r-3 file:border-b-3 file:bg-white file:px-4 file:py-2 file:text-sm file:tinos-bold file:text-black hover:file:bg-gray-100">
        </div>
        <div class="flex-1">
            <label for="alt_text" class="block text-sm tinos-bold text-black">Alt Text</label>
            <input type="text" name="alt_text" id="alt_text" placeholder="Describe the image..."
                   class="mt-1 block w-full border-black border border-r-3 border-b-3 px-3 py-2 tinos-regular focus:outline-none">
        </div>
        <button type="submit" class="bg-black text-white border-black border border-r-3 border-b-3 px-4 py-2 text-sm tinos-bold hover:bg-gray-800">Upload</button>
    </form>
    @error('image') <p class="mt-2 text-sm text-black tinos-regular-italic">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
    @forelse($images as $image)
    <div class="group relative bg-white border-black border border-r-3 border-b-3 overflow-hidden">
        <img src="{{ $image->url }}" alt="{{ $image->alt_text }}"
             class="aspect-square w-full object-cover">
        <div class="absolute inset-0 flex flex-col items-center justify-center gap-2 bg-white/80 opacity-0 group-hover:opacity-100">
            <button onclick="copyToClipboard('{{ $image->url }}')"
                    class="bg-white text-black border-black border border-r-3 border-b-3 px-3 py-1 text-xs tinos-bold hover:bg-gray-100">Copy URL</button>
            <button onclick="copyToClipboard('![{{ $image->alt_text }}]({{ $image->url }})')"
                    class="bg-white text-black border-black border border-r-3 border-b-3 px-3 py-1 text-xs tinos-bold hover:bg-gray-100">Copy Markdown</button>
            <form method="POST" action="{{ route('images.destroy', $image) }}"
                  onsubmit="return confirm('Delete this image?')">
                @csrf @method('DELETE')
                <button type="submit" class="bg-black text-white border-black border border-r-3 border-b-3 px-3 py-1 text-xs tinos-bold hover:bg-gray-800">Delete</button>
            </form>
        </div>
        <div class="p-2">
            <p class="truncate text-xs text-gray-600 tinos-regular" title="{{ $image->original_filename }}">{{ $image->original_filename }}</p>
        </div>
    </div>
    @empty
    <div class="col-span-full py-8 text-center text-sm text-gray-600 tinos-regular">No images uploaded yet.</div>
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
