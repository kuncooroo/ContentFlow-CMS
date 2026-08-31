<div class="space-y-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
    <div>
        <h3 class="text-sm font-semibold text-slate-900">SEO</h3>
        <p class="mt-1 text-sm text-slate-600">
            Leave fields blank to inherit site defaults. The title and excerpt or body are used when SEO overrides are empty.
        </p>
    </div>

    <div>
        <label for="seo_title" class="block text-sm font-medium text-slate-700">SEO title</label>
        <input
            id="seo_title"
            type="text"
            wire:model="seo_title"
            placeholder="Optional override"
            class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
        >
        @error('seo_title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="meta_description" class="block text-sm font-medium text-slate-700">Meta description</label>
        <textarea
            id="meta_description"
            wire:model="meta_description"
            rows="3"
            placeholder="Optional override (max 320 characters)"
            class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
        ></textarea>
        @error('meta_description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="canonical_url" class="block text-sm font-medium text-slate-700">Canonical URL</label>
        <input
            id="canonical_url"
            type="text"
            wire:model="canonical_url"
            placeholder="https://example.com/page or /page"
            class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
        >
        @error('canonical_url')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="og_media_id" class="block text-sm font-medium text-slate-700">Open Graph image</label>
        <select
            id="og_media_id"
            wire:model="og_media_id"
            class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
        >
            <option value="">None (use site default or featured image)</option>
            @foreach ($mediaItems as $media)
                <option value="{{ $media->id }}">{{ $media->original_name }}</option>
            @endforeach
        </select>
        @error('og_media_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <label class="flex items-center gap-2 text-sm text-slate-700">
        <input
            type="checkbox"
            wire:model="robots_index"
            class="rounded border-slate-300 text-slate-900 focus:ring-slate-500"
        >
        Allow search engines to index this page
    </label>
    @error('robots_index')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
