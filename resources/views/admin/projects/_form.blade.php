<div class="space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
        <input type="text" name="name" id="name" required
               value="{{ old('name', $project->name ?? '') }}"
               class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div>
            <label for="url" class="block text-sm font-medium text-gray-700">URL</label>
            <input type="url" name="url" id="url"
                   value="{{ old('url', $project->url ?? '') }}"
                   class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
            @error('url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="url_label" class="block text-sm font-medium text-gray-700">URL Label</label>
            <input type="text" name="url_label" id="url_label"
                   value="{{ old('url_label', $project->url_label ?? '') }}"
                   class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
            @error('url_label') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="image" class="block text-sm font-medium text-gray-700">Image URL</label>
        <input type="text" name="image" id="image"
               value="{{ old('image', $project->image ?? '') }}"
               class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
        @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" id="description" rows="3"
                  class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">{{ old('description', $project->description ?? '') }}</textarea>
        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="charge" class="block text-sm font-medium text-gray-700">Role / Charge</label>
        <input type="text" name="charge" id="charge"
               value="{{ old('charge', $project->charge ?? '') }}"
               class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500">
        @error('charge') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex gap-6">
        <label class="flex items-center gap-2">
            <input type="hidden" name="dark_mode" value="0">
            <input type="checkbox" name="dark_mode" value="1"
                   {{ old('dark_mode', $project->dark_mode ?? false) ? 'checked' : '' }}
                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="text-sm text-gray-700">Dark mode</span>
        </label>

        <label class="flex items-center gap-2">
            <input type="hidden" name="is_visible" value="0">
            <input type="checkbox" name="is_visible" value="1"
                   {{ old('is_visible', $project->is_visible ?? true) ? 'checked' : '' }}
                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="text-sm text-gray-700">Visible</span>
        </label>
    </div>
</div>
