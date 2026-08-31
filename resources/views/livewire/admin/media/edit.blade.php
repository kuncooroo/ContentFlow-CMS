<div class="mx-auto max-w-3xl space-y-6">
    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="aspect-video bg-slate-100">
            @if (str_starts_with($media->mime_type, 'image/'))
                <img
                    src="{{ $media->url() }}"
                    alt="{{ $media->alt_text ?: $media->original_name }}"
                    class="h-full w-full object-contain"
                >
            @endif
        </div>
        <div class="space-y-1 p-4 text-sm text-slate-600">
            <p><span class="font-medium text-slate-900">Original name:</span> {{ $media->original_name }}</p>
            <p><span class="font-medium text-slate-900">MIME type:</span> {{ $media->mime_type }}</p>
            <p><span class="font-medium text-slate-900">Size:</span> {{ number_format($media->size_bytes / 1024, 1) }} KB</p>
            @if ($media->width && $media->height)
                <p><span class="font-medium text-slate-900">Dimensions:</span> {{ $media->width }}×{{ $media->height }}</p>
            @endif
        </div>
    </div>

    <form wire:submit="save" class="space-y-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <div>
            <label for="alt_text" class="block text-sm font-medium text-slate-700">Alt text</label>
            <textarea
                id="alt_text"
                wire:model="alt_text"
                rows="3"
                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500"
            ></textarea>
            @error('alt_text')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.media.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Back to library</a>
            <button
                type="submit"
                class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800"
            >
                Save metadata
            </button>
        </div>
    </form>
</div>
