<div class="space-y-6">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
        <input type="text" name="title" id="title" required
               value="{{ old('title', $post->title ?? '') }}"
               class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="excerpt" class="block text-sm font-medium text-gray-700">Excerpt</label>
        <textarea name="excerpt" id="excerpt" rows="2"
                  class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
        @error('excerpt') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="body" class="block text-sm font-medium text-gray-700">Body (Markdown) *</label>
        <textarea name="body" id="body" rows="16" required
                  class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 font-mono text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">{{ old('body', $post->body ?? '') }}</textarea>
        @error('body') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="featured_image" class="block text-sm font-medium text-gray-700">Featured Image</label>
        @if(!empty($post->featured_image))
            <div class="mt-1 mb-2">
                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="" class="h-32 rounded-md object-cover">
            </div>
        @endif
        <input type="file" name="featured_image" id="featured_image" accept="image/*"
               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100">
        @error('featured_image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Categories</label>
        <div class="flex flex-wrap gap-3">
            @foreach($categories as $category)
                <label class="flex items-center gap-1.5">
                    <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                           {{ in_array($category->id, old('categories', isset($post) ? $post->categories->pluck('id')->toArray() : [])) ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">{{ $category->name }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <div>
        <label for="tags_input" class="block text-sm font-medium text-gray-700">Tags (comma-separated)</label>
        <input type="text" name="tags_input" id="tags_input"
               value="{{ old('tags_input', isset($post) ? $post->tags->pluck('name')->implode(', ') : '') }}"
               placeholder="laravel, php, tutorial"
               class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
        @error('tags_input') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
        <input type="text" name="meta_description" id="meta_description" maxlength="255"
               value="{{ old('meta_description', $post->meta_description ?? '') }}"
               class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
        @error('meta_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
        <select name="status" id="status" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
            <option value="draft" {{ old('status', $post->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ old('status', $post->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
        </select>
    </div>
</div>
