<div class="space-y-6">
    <div>
        <label for="name" class="block text-sm tinos-bold text-black">Name *</label>
        <input type="text" name="name" id="name" required
               value="{{ old('name', $trackingLink->name ?? '') }}"
               class="mt-1 block w-full border-black border border-r-3 border-b-3 px-3 py-2 tinos-regular focus:outline-none">
        @error('name') <p class="mt-1 text-sm text-black tinos-regular-italic">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="code" class="block text-sm tinos-bold text-black">Code *</label>
        <div class="mt-1 flex items-center gap-2">
            <span class="text-sm text-gray-600 tinos-regular">?ref=</span>
            <input type="text" name="code" id="code" required
                   value="{{ old('code', $trackingLink->code ?? '') }}"
                   pattern="[a-z0-9\-]+"
                   class="block w-full border-black border border-r-3 border-b-3 px-3 py-2 tinos-regular focus:outline-none">
        </div>
        <p class="mt-1 text-xs text-gray-500 tinos-regular">Lowercase letters, numbers and hyphens only. Use as <code>?ref=code</code> in any URL.</p>
        @error('code') <p class="mt-1 text-sm text-black tinos-regular-italic">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                   {{ old('is_active', $trackingLink->is_active ?? true) ? 'checked' : '' }}
                   class="h-4 w-4 border-black accent-black">
            <span class="text-sm text-black">Active</span>
        </label>
    </div>
</div>
