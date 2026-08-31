<div class="space-y-6">
@error('media')
        <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ $message }}
        </div>
    @enderror

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div class="flex-1 space-y-3">
            <div>
                <label for="search" class="block text-sm font-medium text-slate-700">Search</label>
                <input
                    id="search"
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by filename or alt text"
                    class="mt-1 block w-full max-w-md rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
                >
            </div>
            @if ($hasActiveFilters)
                <button type="button" wire:click="clearFilters" class="text-sm font-medium text-slate-600 hover:text-slate-900">
                    Clear filters
                </button>
            @endif
        </div>

        @can('upload', App\Models\Media::class)
            <form wire:submit="storeUpload" class="flex flex-col gap-2 sm:flex-row sm:items-end">
                <div>
                    <label for="upload" class="block text-sm font-medium text-slate-700">Upload</label>
                    <input
                        id="upload"
                        type="file"
                        wire:model="upload"
                        accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp"
                        class="mt-1 block w-full text-sm text-slate-600"
                    >
                    @error('upload')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button
                    type="submit"
                    class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 disabled:opacity-50"
                    wire:loading.attr="disabled"
                    wire:target="upload,storeUpload"
                >
                    Upload file
                </button>
            </form>
        @endcan
    </div>

    <div wire:loading wire:target="upload,storeUpload" class="text-sm text-slate-500">
        Uploading…
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($mediaItems as $item)
            <div wire:key="media-{{ $item->id }}" class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="aspect-video bg-slate-100">
                    @if (str_starts_with($item->mime_type, 'image/'))
                        <img
                            src="{{ $item->url() }}"
                            alt="{{ $item->alt_text ?: $item->original_name }}"
                            class="h-full w-full object-cover"
                        >
                    @else
                        <div class="flex h-full items-center justify-center text-sm text-slate-500">
                            {{ $item->mime_type }}
                        </div>
                    @endif
                </div>
                <div class="space-y-2 p-4 text-sm">
                    <p class="truncate font-medium text-slate-900" title="{{ $item->original_name }}">
                        {{ $item->original_name }}
                    </p>
                    <p class="text-xs text-slate-500">
                        {{ number_format($item->size_bytes / 1024, 1) }} KB
                        @if ($item->width && $item->height)
                            · {{ $item->width }}×{{ $item->height }}
                        @endif
                    </p>
                    @if ($item->alt_text)
                        <p class="truncate text-xs text-slate-600" title="{{ $item->alt_text }}">
                            Alt: {{ $item->alt_text }}
                        </p>
                    @endif
                    <div class="flex items-center gap-3 pt-1">
                        @can('update', $item)
                            <a href="{{ route('admin.media.edit', $item) }}" class="text-slate-600 hover:text-slate-900">
                                Edit
                            </a>
                        @endcan
                        @can('delete', $item)
                            <button
                                type="button"
                                wire:click="delete({{ $item->id }})"
                                wire:confirm="Delete this media item? This cannot be undone."
                                class="text-red-600 hover:text-red-800"
                            >
                                Delete
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-lg border border-dashed border-slate-300 bg-white px-6 py-12 text-center text-sm text-slate-500">
                No media matches your search.
            </div>
        @endforelse
    </div>

    <div>
        {{ $mediaItems->links() }}
    </div>
</div>
