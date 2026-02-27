<div class="space-y-6">
    <div>
        <label for="title" class="block text-sm tinos-bold text-black">Title *</label>
        <input type="text" name="title" id="title" required
               value="{{ old('title', $post->title ?? '') }}"
               class="mt-1 block w-full border-black border border-r-3 border-b-3 px-3 py-2 tinos-regular focus:outline-none">
        @error('title') <p class="mt-1 text-sm text-black tinos-regular-italic">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="excerpt" class="block text-sm tinos-bold text-black">Excerpt</label>
        <textarea name="excerpt" id="excerpt" rows="2"
                  class="mt-1 block w-full border-black border border-r-3 border-b-3 px-3 py-2 tinos-regular focus:outline-none">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
        @error('excerpt') <p class="mt-1 text-sm text-black tinos-regular-italic">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="body" class="block text-sm tinos-bold text-black">Body (Markdown) *</label>
        <textarea name="body" id="body" rows="16" required
                  class="mt-1 block w-full border-black border border-r-3 border-b-3 px-3 py-2 font-mono text-sm focus:outline-none">{{ old('body', $post->body ?? '') }}</textarea>
        @error('body') <p class="mt-1 text-sm text-black tinos-regular-italic">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="featured_image" class="block text-sm tinos-bold text-black">Featured Image</label>
        @if(!empty($post->featured_image))
            <div class="mt-1 mb-2">
                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="" class="h-32 object-cover border border-black">
            </div>
        @endif
        <input type="file" name="featured_image" id="featured_image" accept="image/*"
               class="mt-1 block w-full text-sm text-black file:mr-4 file:border-black file:border file:border-r-3 file:border-b-3 file:bg-white file:px-4 file:py-2 file:text-sm file:tinos-bold file:text-black hover:file:bg-gray-100">
        @error('featured_image') <p class="mt-1 text-sm text-black tinos-regular-italic">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm tinos-bold text-black mb-2">Categories</label>
        <div class="flex flex-wrap gap-3">
            @foreach($categories as $category)
                <label class="flex items-center gap-1.5">
                    <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                           {{ in_array($category->id, old('categories', isset($post) ? $post->categories->pluck('id')->toArray() : [])) ? 'checked' : '' }}
                           class="h-4 w-4 border-black accent-black">
                    <span class="text-sm text-black">{{ $category->name }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <div>
        <label for="tags_input" class="block text-sm tinos-bold text-black">Tags (comma-separated)</label>
        <input type="text" name="tags_input" id="tags_input"
               value="{{ old('tags_input', isset($post) ? $post->tags->pluck('name')->implode(', ') : '') }}"
               placeholder="laravel, php, tutorial"
               class="mt-1 block w-full border-black border border-r-3 border-b-3 px-3 py-2 tinos-regular focus:outline-none">
        @error('tags_input') <p class="mt-1 text-sm text-black tinos-regular-italic">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="meta_description" class="block text-sm tinos-bold text-black">Meta Description</label>
        <input type="text" name="meta_description" id="meta_description" maxlength="255"
               value="{{ old('meta_description', $post->meta_description ?? '') }}"
               class="mt-1 block w-full border-black border border-r-3 border-b-3 px-3 py-2 tinos-regular focus:outline-none">
        @error('meta_description') <p class="mt-1 text-sm text-black tinos-regular-italic">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="status" class="block text-sm tinos-bold text-black">Status *</label>
        <select name="status" id="status" required
                class="mt-1 block w-full border-black border border-r-3 border-b-3 px-3 py-2 tinos-regular focus:outline-none bg-white">
            <option value="draft" {{ old('status', $post->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ old('status', $post->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
        </select>
    </div>
</div>
