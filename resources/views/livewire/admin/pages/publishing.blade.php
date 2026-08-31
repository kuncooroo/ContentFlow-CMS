@php
    use App\Enums\PageStatus;
@endphp

<div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm text-slate-600">
                Status:
                <span class="font-medium text-slate-900">{{ $page->status->label() }}</span>
            </p>
            @if ($page->status === PageStatus::Published && $page->publish_at)
                <p class="mt-1 text-xs text-slate-500">
                    Published {{ $page->publish_at->format('M j, Y g:i A') }}
                </p>
            @endif
        </div>

        @can('preview', $page)
            <a
                href="{{ route('admin.pages.preview', $page) }}"
                target="_blank"
                class="text-sm font-medium text-slate-600 hover:text-slate-900"
            >
                Preview
            </a>
        @endcan
    </div>

    @error('status')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
    @error('page')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror

    <div class="mt-4 flex flex-wrap gap-2">
        @if ($page->status === PageStatus::Draft)
            @can('publish', $page)
                <button
                    type="button"
                    wire:click="publish"
                    class="rounded-md bg-green-700 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-800"
                >
                    Publish
                </button>
            @endcan
        @endif

        @if ($page->status === PageStatus::Published)
            @can('archive', $page)
                <button
                    type="button"
                    wire:click="archive"
                    wire:confirm="Archive this page? It will be removed from public listings."
                    class="rounded-md border border-slate-300 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Archive
                </button>
            @endcan

            @can('unpublish', $page)
                <button
                    type="button"
                    wire:click="unpublish"
                    wire:confirm="Unpublish this page and return it to draft?"
                    class="rounded-md border border-amber-300 bg-amber-50 px-3 py-1.5 text-sm font-medium text-amber-900 hover:bg-amber-100"
                >
                    Unpublish to draft
                </button>
            @endcan
        @endif

        @if ($page->status === PageStatus::Archived)
            @can('restore', $page)
                <button
                    type="button"
                    wire:click="restoreToDraft"
                    class="rounded-md bg-slate-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-slate-800"
                >
                    Restore to draft
                </button>
            @endcan
        @endif
    </div>
</div>
